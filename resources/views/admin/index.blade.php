@extends('layouts.app')
@section('title','Admin Control Center')
@section('subtitle','System health, users and content')

@section('content')
<div class="space-y-6">

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5">
        <div class="rounded-xl border border-[var(--line)] bg-[var(--surface)] p-5">
            <div class="text-[11px] uppercase tracking-[0.12em] text-[var(--muted)] mb-2 font-semibold">Users</div>
            <div class="text-2xl font-bold num">{{ $stats['users'] }}</div>
        </div>
        <div class="rounded-xl border border-[var(--line)] bg-[var(--surface)] p-5">
            <div class="text-[11px] uppercase tracking-[0.12em] text-[var(--muted)] mb-2 font-semibold">Markets</div>
            <div class="text-2xl font-bold num">{{ $stats['markets'] }}</div>
        </div>
        <div class="rounded-xl border border-[var(--line)] bg-[var(--surface)] p-5">
            <div class="text-[11px] uppercase tracking-[0.12em] text-[var(--muted)] mb-2 font-semibold">Signals</div>
            <div class="text-2xl font-bold num">{{ $stats['signals'] }}</div>
        </div>
        <div class="rounded-xl border border-[var(--line)] bg-[var(--surface)] p-5">
            <div class="text-[11px] uppercase tracking-[0.12em] text-[var(--muted)] mb-2 font-semibold">Journal trades</div>
            <div class="text-2xl font-bold num">{{ $stats['trades'] }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Recent Users Table -->
        <div class="lg:col-span-2 rounded-xl border border-[var(--line)] bg-[var(--surface)] overflow-hidden flex flex-col">
            <div class="px-5 py-4 border-b border-[var(--line)] bg-[var(--base)]/40">
                <h3 class="text-[13px] font-semibold uppercase tracking-[0.14em] m-0">Recent users</h3>
            </div>
            
            <div class="overflow-x-auto thin-scroll flex-1">
                <table class="w-full text-[13px] min-w-[500px] m-0 border-collapse">
                    <thead>
                        <tr class="text-[10px] uppercase tracking-[0.12em] text-[var(--muted)] border-b border-[var(--line)]">
                            <th class="text-left font-medium px-5 py-3 border-none">Name</th>
                            <th class="text-left font-medium px-3 py-3 border-none">Email</th>
                            <th class="text-left font-medium px-3 py-3 border-none">Role</th>
                            <th class="text-left font-medium px-5 py-3 border-none">Joined</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--line)]">
                        @foreach($users as $user)
                        <tr class="hover:bg-[var(--raised)]/50 transition-colors">
                            <td class="px-5 py-3.5 font-semibold border-none">{{ $user->name }}</td>
                            <td class="px-3 py-3.5 text-[var(--muted)] border-none">{{ $user->email }}</td>
                            <td class="px-3 py-3.5 border-none">
                                <form method="POST" action="{{ route('admin.users.role', $user) }}" class="m-0">
                                    @csrf @method('PATCH')
                                    <select name="role" onchange="this.form.submit()" class="bg-[var(--raised)] border border-[var(--line)] text-[var(--text)] text-[11px] font-semibold px-2 py-1 rounded outline-none cursor-pointer hover:border-[var(--brand)]/50 transition-colors focus:border-[var(--brand)] focus:ring-1 focus:ring-[var(--brand)]">
                                        <option value="trader" @selected($user->role==='trader')>Trader</option>
                                        <option value="admin" @selected($user->role==='admin')>Admin</option>
                                    </select>
                                </form>
                            </td>
                            <td class="num px-5 py-3.5 text-[11px] text-[var(--muted)] border-none">{{ $user->created_at->diffForHumans() }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- System Status -->
        <div class="rounded-xl border border-[var(--line)] bg-[var(--surface)] overflow-hidden flex flex-col">
            <div class="px-5 py-4 border-b border-[var(--line)] bg-[var(--base)]/40">
                <h3 class="text-[13px] font-semibold uppercase tracking-[0.14em] m-0">System status</h3>
            </div>
            <div class="p-5 flex flex-col gap-4">
                <div class="flex items-center justify-between text-[13px]">
                    <span class="flex items-center gap-2.5 text-[var(--muted)]">
                        <span class="relative w-1.5 h-1.5 rounded-full bg-[var(--up)] text-[var(--up)] pulse"></span>
                        Market engine
                    </span>
                    <strong class="text-[var(--up)] font-semibold">Online</strong>
                </div>
                <div class="flex items-center justify-between text-[13px]">
                    <span class="text-[var(--muted)]">News records</span>
                    <strong class="num font-semibold">{{ $stats['news'] }}</strong>
                </div>
                <div class="flex items-center justify-between text-[13px]">
                    <span class="text-[var(--muted)]">Scheduler</span>
                    <strong class="text-[11px] font-bold px-2 py-0.5 rounded border border-[var(--brand)]/25 bg-[var(--brand)]/10 text-[var(--brand)] tracking-wide">HOURLY</strong>
                </div>
                <div class="flex items-center justify-between text-[13px]">
                    <span class="text-[var(--muted)]">Queue</span>
                    <strong class="text-[11px] font-bold px-2 py-0.5 rounded border border-[var(--muted)]/25 bg-[var(--muted)]/10 text-[var(--muted)] tracking-wide">SYNC</strong>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
