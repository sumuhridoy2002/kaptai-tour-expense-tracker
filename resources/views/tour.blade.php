@php
$money = fn($p) => number_format($p / 100, 2);
@endphp
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Kaptai Lake · Office Tour</title><link rel="stylesheet" href="{{ asset('tour.css') }}"><script defer src="{{ asset('tour.js') }}"></script></head><body>
<main>
<header><a class="brand" href="{{ route('tour') }}"><span class="logo">≈</span> OFFICE ESCAPES</a><span class="tag">TOUR EXPENSE TRACKER</span></header>
<section class="hero"><div><span class="eyebrow">THE TEAM. THE LAKE. THE MEMORIES.</span><h1>Kaptai Lake <span>office tour.</span></h1><p>Every contribution and every expense, together in one place.</p></div><div class="hero-art" aria-hidden="true"><div class="sun"></div><div class="hill one"></div><div class="hill two"></div><div class="water">≈ ≈ ≈</div></div></section>
@if(session('success'))<div role="status" class="notice">✓ {{ session('success') }}</div>@endif
<section class="stats" aria-label="Overall totals">
<div class="stat"><small>TOTAL DEPOSITED</small><strong>৳ {{ $money($deposited) }}</strong><span>{{ $members->count() }} team members</span></div>
<div class="stat"><small>TOTAL EXPENSES</small><strong>৳ {{ $money($spent) }}</strong><span>{{ $expenses->count() }} expense entries</span></div>
<div class="stat balance {{ $deposited < $spent ? 'negative' : '' }}"><small>{{ $deposited < $spent ? 'BUDGET SHORTFALL' : 'REMAINING BALANCE' }}</small><strong>৳ {{ $money(abs($deposited-$spent)) }}</strong><span>{{ $deposited < $spent ? 'Expenses exceed deposits' : 'Available for the trip' }}</span></div>
</section>
<div class="grid"><section class="panel"><div class="panel-head"><div><h2>Team members</h2><p>Contributions for the trip</p></div><button data-open="member">+ Add member</button></div>
<label class="search"><span>Search members</span><input type="search" data-search="members" placeholder="Search by name or amount…"></label>
<div class="table-wrap"><table id="members"><thead><tr><th>Member</th><th class="num">Deposited</th></tr></thead><tbody>
@forelse($members as $member)<tr data-row><td><span class="avatar">{{ mb_substr($member->name,0,1) }}</span>{{ $member->name }}</td><td class="num">৳ {{ $money($member->amount_paisa) }}</td></tr>@empty<tr><td colspan="2" class="empty">No members yet. Add your first team member.</td></tr>@endforelse
<tr data-no-results hidden><td colspan="2" class="empty">No matching members.</td></tr></tbody><tfoot><tr><td>Total deposited</td><td class="num">৳ {{ $money($deposited) }}</td></tr></tfoot></table></div><p class="hint">Member entries are permanent. Please check before saving.</p></section>
<section class="panel"><div class="panel-head"><div><h2>Categories</h2><p>Where the money goes</p></div><button class="secondary" data-open="category">+ Add category</button></div>
<label class="search"><span>Search categories</span><input type="search" data-search="categories" placeholder="Search categories…"></label>
<div class="table-wrap"><table id="categories"><thead><tr><th>Category</th><th class="num">Spent</th></tr></thead><tbody>
@forelse($categories as $category)<tr data-row><td>{{ $category->name }}</td><td class="num">৳ {{ $money($category->total_paisa) }}</td></tr>@empty<tr><td colspan="2" class="empty">Add categories such as Transport, Food or Boat.</td></tr>@endforelse
<tr data-no-results hidden><td colspan="2" class="empty">No matching categories.</td></tr></tbody><tfoot><tr><td>Total spent</td><td class="num">৳ {{ $money($spent) }}</td></tr></tfoot></table></div></section></div>
<section class="panel expenses"><div class="panel-head"><div><h2>Expense ledger</h2><p>All tour expenses, newest date first</p></div><button data-open="expense" @disabled($categories->isEmpty())>+ Add expense</button></div>
@if($categories->isEmpty())<p class="hint">Create a category first to start recording expenses.</p>@endif
<div class="filters"><label class="search"><span>Search expenses</span><input type="search" data-search="expenses" placeholder="Search title, category, date or amount…"></label><label class="category-filter"><span>Category</span><select id="category-filter"><option value="">All categories</option>@foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select></label></div>
<div class="table-wrap"><table id="expenses"><thead><tr><th>Date</th><th>Expense</th><th>Category</th><th class="num">Amount</th></tr></thead><tbody>
@forelse($expenses as $expense)<tr data-row data-category="{{ $expense->category_id }}" data-amount="{{ $expense->amount_paisa }}"><td class="muted">{{ $expense->spent_on }}</td><td>{{ $expense->title }}</td><td><span class="pill">{{ $expense->category }}</span></td><td class="num">৳ {{ $money($expense->amount_paisa) }}</td></tr>@empty<tr><td colspan="4" class="empty">Your expense ledger is empty. Start with the first trip expense.</td></tr>@endforelse
<tr data-no-results hidden><td colspan="4" class="empty">No matching expenses.</td></tr></tbody><tfoot><tr><td colspan="3">All expenses total</td><td class="num">৳ {{ $money($spent) }}</td></tr></tfoot></table></div><p class="hint" id="filtered-total" aria-live="polite"></p></section>
<footer>KAPTAI LAKE · OFFICE TOUR <span>Simple plans. Clear accounts.</span></footer>
</main>
@foreach(['member'=>'Add team member','category'=>'Add expense category','expense'=>'Record expense'] as $kind=>$heading)
<dialog id="{{ $kind }}" data-reopen="{{ $errors->getBag($kind)->any() ? 'true' : 'false' }}" aria-labelledby="{{ $kind }}-title"><div class="modal-heading"><h2 id="{{ $kind }}-title">{{ $heading }}</h2><button type="button" data-close aria-label="Close" class="close">×</button></div>
<form method="post" action="{{ route($kind === 'member' ? 'members.store' : ($kind === 'category' ? 'categories.store' : 'expenses.store')) }}">@csrf
@if($errors->getBag($kind)->any())<div class="errors" role="alert"><ul>@foreach($errors->getBag($kind)->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@php($failed = $errors->getBag($kind)->any())
@if($kind !== 'category')<input type="hidden" name="submission_id" value="{{ $failed ? old('submission_id', (string) Str::uuid()) : (string) Str::uuid() }}">@endif
@if($kind === 'expense')
<label>Expense title<input name="title" maxlength="180" required value="{{ $failed ? old('title') : '' }}" placeholder="e.g. Kaptai boat rental"></label>
<label>Category<select name="category_id" required><option value="">Select category</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected($failed && old('category_id') == $category->id)>{{ $category->name }}</option>@endforeach</select></label>
<label>Date<input type="date" name="spent_on" required value="{{ $failed ? old('spent_on', now()->toDateString()) : now()->toDateString() }}"></label>
@else
<label>{{ $kind === 'member' ? 'Member name' : 'Category name' }}<input name="name" maxlength="{{ $kind === 'member' ? 120 : 80 }}" required value="{{ $failed ? old('name') : '' }}" placeholder="{{ $kind === 'member' ? 'Full name' : 'e.g. Transport' }}"></label>
@endif
@if($kind !== 'category')<label>{{ $kind === 'member' ? 'Deposited amount' : 'Expense amount' }} (৳)<input name="amount" type="number" inputmode="decimal" min="0.01" max="99999999.99" step="0.01" required value="{{ $failed ? old('amount') : '' }}" placeholder="0.00"></label>@endif
<div class="modal-actions"><button type="button" class="secondary" data-close>Cancel</button><button type="submit">{{ $kind === 'category' ? 'Add category' : 'Save entry' }}</button></div></form></dialog>
@endforeach
</body></html>
