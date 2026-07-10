<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountingController extends Controller
{
    public function index()
    {
        $bankAccounts = BankAccount::active()->get();
        $totalBalance = $bankAccounts->sum('current_balance');
        $recentEntries = JournalEntry::with('creator')->latest()->limit(10)->get();
        $totalDebit = JournalEntry::where('status', 'posted')->sum('total_debit');
        $totalCredit = JournalEntry::where('status', 'posted')->sum('total_credit');

        return view('admin.accounting.index', compact('bankAccounts', 'totalBalance', 'recentEntries', 'totalDebit', 'totalCredit'));
    }

    // Bank Accounts
    public function bankAccounts()
    {
        $accounts = BankAccount::withCount('journalEntryItems')->latest()->get();
        return view('admin.accounting.bank-accounts', compact('accounts'));
    }

    public function bankAccountCreate()
    {
        return view('admin.accounting.bank-account-form');
    }

    public function bankAccountStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'bank_name' => 'required|string|max:200',
            'account_number' => 'nullable|string|max:50',
            'account_type' => 'required|in:savings,checking,credit,cash_wallet',
            'opening_balance' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'notes' => 'nullable|string',
        ]);

        if (BankAccount::count() === 0) {
            $validated['is_default'] = true;
        }

        $validated['current_balance'] = $validated['opening_balance'] ?? 0;

        BankAccount::create($validated);

        return redirect()->route('admin.accounting.bank-accounts')->with('success', 'Bank account created successfully!');
    }

    public function bankAccountEdit(BankAccount $bankAccount)
    {
        return view('admin.accounting.bank-account-form', ['account' => $bankAccount]);
    }

    public function bankAccountUpdate(Request $request, BankAccount $bankAccount)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'bank_name' => 'required|string|max:200',
            'account_number' => 'nullable|string|max:50',
            'account_type' => 'required|in:savings,checking,credit,cash_wallet',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $bankAccount->update($validated);

        return redirect()->route('admin.accounting.bank-accounts')->with('success', 'Bank account updated successfully!');
    }

    public function bankAccountDestroy(BankAccount $bankAccount)
    {
        if ($bankAccount->is_default) {
            return back()->with('error', 'Cannot delete the default bank account.');
        }
        if ($bankAccount->journalEntryItems()->count() > 0) {
            return back()->with('error', 'Cannot delete bank account with existing transactions.');
        }

        $bankAccount->delete();

        return redirect()->route('admin.accounting.bank-accounts')->with('success', 'Bank account deleted successfully!');
    }

    // Journal Entries
    public function journalEntries(Request $request)
    {
        $query = JournalEntry::with('creator');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('date_from')) {
            $query->where('entry_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('entry_date', '<=', $request->date_to);
        }

        $entries = $query->latest('entry_date')->paginate(30);

        return view('admin.accounting.journal-entries', compact('entries'));
    }

    public function journalEntryCreate()
    {
        $bankAccounts = BankAccount::active()->get();
        return view('admin.accounting.journal-entry-form', ['bankAccounts' => $bankAccounts, 'entry' => null]);
    }

    public function journalEntryStore(Request $request)
    {
        $validated = $request->validate([
            'entry_date' => 'required|date',
            'type' => 'required|in:general,payment,receipt,journal,adjustment',
            'description' => 'required|string|max:500',
            'items' => 'required|array|min:2',
            'items.*.account_code' => 'required|string|max:50',
            'items.*.account_name' => 'required|string|max:200',
            'items.*.debit' => 'nullable|numeric|min:0',
            'items.*.credit' => 'nullable|numeric|min:0',
            'items.*.description' => 'nullable|string',
            'items.*.bank_account_id' => 'nullable|exists:bank_accounts,id',
        ]);

        $totalDebit = collect($validated['items'])->sum('debit');
        $totalCredit = collect($validated['items'])->sum('credit');

        if (abs($totalDebit - $totalCredit) > 0.01) {
            return back()->withInput()->with('error', 'Debit and credit must be equal.');
        }

        DB::transaction(function () use ($validated, $totalDebit, $totalCredit) {
            $entry = JournalEntry::create([
                'entry_date' => $validated['entry_date'],
                'type' => $validated['type'],
                'description' => $validated['description'],
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            foreach ($validated['items'] as $item) {
                JournalEntryItem::create([
                    'journal_entry_id' => $entry->id,
                    'account_code' => $item['account_code'],
                    'account_name' => $item['account_name'],
                    'debit' => $item['debit'] ?? 0,
                    'credit' => $item['credit'] ?? 0,
                    'description' => $item['description'] ?? null,
                    'bank_account_id' => $item['bank_account_id'] ?? null,
                ]);
            }
        });

        return redirect()->route('admin.accounting.journal-entries')->with('success', 'Journal entry created successfully!');
    }

    public function journalEntryShow(JournalEntry $entry)
    {
        $entry->load(['items.bankAccount', 'creator']);
        return view('admin.accounting.journal-entry-show', compact('entry'));
    }

    public function journalEntryPost(JournalEntry $entry)
    {
        if ($entry->status !== 'draft') {
            return back()->with('error', 'Only draft entries can be posted.');
        }

        if (!$entry->isBalanced()) {
            return back()->with('error', 'Entry is not balanced. Debit must equal credit.');
        }

        DB::transaction(function () use ($entry) {
            foreach ($entry->items as $item) {
                if ($item->bank_account_id) {
                    $bankAccount = BankAccount::find($item->bank_account_id);
                    if ($bankAccount) {
                        if ($item->debit > 0) {
                            $bankAccount->increment('current_balance', $item->debit);
                        } elseif ($item->credit > 0) {
                            $bankAccount->decrement('current_balance', $item->credit);
                        }
                    }
                }
            }

            $entry->update(['status' => 'posted']);
        });

        return back()->with('success', 'Journal entry posted successfully!');
    }

    public function journalEntryVoid(JournalEntry $entry)
    {
        if ($entry->status !== 'posted') {
            return back()->with('error', 'Only posted entries can be voided.');
        }

        DB::transaction(function () use ($entry) {
            foreach ($entry->items as $item) {
                if ($item->bank_account_id) {
                    $bankAccount = BankAccount::find($item->bank_account_id);
                    if ($bankAccount) {
                        if ($item->debit > 0) {
                            $bankAccount->decrement('current_balance', $item->debit);
                        } elseif ($item->credit > 0) {
                            $bankAccount->increment('current_balance', $item->credit);
                        }
                    }
                }
            }

            $entry->update(['status' => 'voided']);
        });

        return back()->with('success', 'Journal entry voided successfully!');
    }

    // Trial Balance
    public function trialBalance()
    {
        $postedEntries = JournalEntry::where('status', 'posted')->get();
        $items = $postedEntries->flatMap(function ($entry) {
            return $entry->items->map(function ($item) {
                return [
                    'account_code' => $item->account_code,
                    'account_name' => $item->account_name,
                    'debit' => $item->debit,
                    'credit' => $item->credit,
                ];
            });
        });

        $trialBalance = $items->groupBy('account_code')->map(function ($group, $code) {
            return [
                'account_code' => $code,
                'account_name' => $group->first()['account_name'],
                'debit' => $group->sum('debit'),
                'credit' => $group->sum('credit'),
            ];
        })->values();

        $totalDebit = $trialBalance->sum('debit');
        $totalCredit = $trialBalance->sum('credit');

        return view('admin.accounting.trial-balance', compact('trialBalance', 'totalDebit', 'totalCredit'));
    }
}
