@extends('layouts.admin')

@section('title', 'Account Settings')

@push('styles')
<style>
/* ─── Account Settings ─────────────────────────────── */
.ac-wrap {
    max-width: 1100px;
    margin: 0 auto;
    padding: 2rem 1rem 3rem;
}

/* 3-column grid */
.ac-grid {
    display: grid;
    grid-template-columns: 240px 1fr 1fr;
    gap: 1rem;
}
@media (max-width: 900px) {
    .ac-grid { grid-template-columns: 1fr; }
    .ac-col-span { grid-column: 1 / -1 !important; }
}

/* Cards */
.ac-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: .875rem;
    box-shadow: 0 1px 8px rgba(0,0,0,.05);
    overflow: hidden;
}
.ac-card-body { padding: 1.5rem; }

/* ─── Identity card ─── */
.ac-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #15803d, #22c55e);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
    font-weight: 700;
    flex-shrink: 0;
    user-select: none;
    letter-spacing: .04em;
    box-shadow: 0 4px 16px rgba(21,128,61,.3);
}
.ac-id-name {
    font-size: .95rem;
    font-weight: 700;
    color: #111827;
    margin-top: 1rem;
}
.ac-id-since {
    font-size: .72rem;
    color: #9ca3af;
    margin-top: .2rem;
}
.ac-id-divider {
    width: 100%;
    border: none;
    border-top: 1px solid #f3f4f6;
    margin: 1.1rem 0 .9rem;
}
.ac-id-email {
    font-size: .8rem;
    color: #15803d;
    font-weight: 600;
    word-break: break-all;
}
.ac-id-phone {
    font-size: .8rem;
    color: #6b7280;
    margin-top: .3rem;
}

/* ─── Panel header ─── */
.ac-panel-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.1rem;
}
.ac-panel-title {
    font-size: .8rem;
    font-weight: 700;
    color: #111827;
}
.ac-edit-btn {
    background: none;
    border: none;
    cursor: pointer;
    color: #d1d5db;
    padding: .2rem;
    line-height: 1;
    border-radius: .375rem;
    transition: color .15s, background .15s;
}
.ac-edit-btn:hover { color: #16a34a; background: #f0fdf4; }

/* ─── dl rows ─── */
.ac-dl { display: flex; flex-direction: column; gap: .85rem; }
.ac-dl-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: .8rem;
    gap: .5rem;
}
.ac-dl-label { color: #9ca3af; flex-shrink: 0; }
.ac-dl-value { color: #1f2937; font-weight: 500; text-align: right; word-break: break-all; }

/* ─── Forms (hidden unless editing) ─── */
.ac-field { margin-bottom: .9rem; }
.ac-field:last-of-type { margin-bottom: 0; }
.ac-label {
    display: block;
    font-size: .75rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: .35rem;
}
.ac-input {
    width: 100%;
    background: #f9fafb;
    border: 1.5px solid #e5e7eb;
    border-radius: .5rem;
    padding: .55rem .8rem;
    font-size: .8rem;
    color: #111827;
    font-family: inherit;
    outline: none;
    transition: border-color .15s, box-shadow .15s, background .15s;
}
.ac-input:focus {
    border-color: #16a34a;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(22,163,74,.1);
}
.ac-input.is-error { border-color: #ef4444; }
.ac-input.is-error:focus { box-shadow: 0 0 0 3px rgba(239,68,68,.1); }
.ac-field-error { font-size: .7rem; color: #ef4444; margin-top: .3rem; }
.ac-field-hint  { font-size: .7rem; color: #9ca3af; margin-top: .3rem; }

/* Password toggle */
.ac-pw-wrap { position: relative; }
.ac-pw-wrap .ac-input { padding-right: 2.5rem; }
.ac-eye {
    position: absolute;
    right: .65rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    color: #9ca3af;
    padding: 0;
    display: flex;
    align-items: center;
    transition: color .15s;
}
.ac-eye:hover { color: #6b7280; }

/* Buttons */
.ac-btn {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    font-size: .78rem;
    font-weight: 600;
    padding: .5rem 1.1rem;
    border-radius: .5rem;
    border: none;
    cursor: pointer;
    font-family: inherit;
    transition: background .15s, box-shadow .15s, transform .1s;
}
.ac-btn:active { transform: scale(.97); }
.ac-btn-primary {
    background: linear-gradient(135deg, #15803d, #16a34a);
    color: #fff;
    box-shadow: 0 2px 8px rgba(21,128,61,.2);
}
.ac-btn-primary:hover {
    background: linear-gradient(135deg, #166534, #15803d);
    box-shadow: 0 4px 12px rgba(21,128,61,.28);
}

/* ─── Sessions ─── */
.ac-session-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: .5rem 0;
    border-bottom: 1px solid #f9fafb;
}
.ac-session-item:last-child { border-bottom: none; }
.ac-session-name { font-size: .8rem; font-weight: 500; color: #1f2937; }
.ac-session-meta { font-size: .7rem; color: #9ca3af; margin-top: .1rem; }
.ac-session-del {
    background: none;
    border: none;
    cursor: pointer;
    color: #d1d5db;
    padding: .2rem;
    display: flex;
    align-items: center;
    border-radius: .375rem;
    transition: color .15s, background .15s;
}
.ac-session-del:hover { color: #ef4444; background: #fef2f2; }

.ac-manage-badge {
    font-size: .65rem;
    font-weight: 700;
    letter-spacing: .06em;
    text-transform: uppercase;
    color: #15803d;
    border: 1px solid #bbf7d0;
    background: #f0fdf4;
    border-radius: 999px;
    padding: .2rem .7rem;
}

/* ─── Full-width bottom row ─── */
.ac-col-span { grid-column: 1 / -1; }

/* Tab bar */
.ac-tabs {
    display: flex;
    border-bottom: 1px solid #e5e7eb;
    padding: 0 .5rem;
    background: #fafafa;
}
.ac-tab {
    padding: .75rem 1rem;
    font-size: .78rem;
    font-weight: 600;
    border: none;
    background: none;
    cursor: pointer;
    font-family: inherit;
    color: #6b7280;
    border-bottom: 2px solid transparent;
    margin-bottom: -1px;
    transition: color .15s, border-color .15s;
    white-space: nowrap;
}
.ac-tab:hover { color: #374151; }
.ac-tab.active { color: #15803d; border-bottom-color: #16a34a; }

/* Activity rows */
.ac-activity-row {
    display: flex;
    align-items: center;
    gap: .9rem;
    padding: .65rem 1rem;
    border-radius: .5rem;
    transition: background .15s;
}
.ac-activity-row:hover { background: #f9fafb; }
.ac-activity-icon {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #f0fdf4;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: #16a34a;
}
.ac-activity-title { font-size: .8rem; font-weight: 500; color: #1f2937; }
.ac-activity-meta  { font-size: .7rem; color: #9ca3af; margin-top: .1rem; }
.ac-status-badge {
    font-size: .68rem;
    font-weight: 600;
    padding: .2rem .7rem;
    border-radius: 999px;
    margin-left: auto;
    flex-shrink: 0;
}
.ac-status-success { background: #f0fdf4; color: #15803d; }

/* Flash */
.ac-flash {
    display: flex;
    align-items: center;
    gap: .5rem;
    border-radius: .625rem;
    padding: .65rem .9rem;
    font-size: .8rem;
    font-weight: 500;
    margin-bottom: 1rem;
    grid-column: 1 / -1;
}
.ac-flash-ok  { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
.ac-flash-err { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; }

/* Edit mode toggling */
[x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
@php
    $defaultTab   = ($errors->has('current_password') || $errors->has('password')) ? 'password' : 'profile';
    $editingField = $errors->has('name') || $errors->has('email') ? 'profile' : (
        ($errors->has('current_password') || $errors->has('password')) ? 'password' : null
    );
    $createdAt = $user->created_at ? $user->created_at->format('M j, Y') : '—';
@endphp

<div class="ac-wrap">
    <div class="ac-grid"
         x-data="{
             editing: '{{ $editingField }}',
             activeTab: 'activity',
             edit(panel) { this.editing = this.editing === panel ? null : panel; }
         }">

        {{-- Flash messages (span full width) --}}
        @if(session('success'))
            <div class="ac-flash ac-flash-ok">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="ac-flash ac-flash-err">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- ── Col 1: Identity card ── --}}
        <div class="ac-card">
            <div class="ac-card-body" style="display:flex;flex-direction:column;align-items:center;text-align:center;">
                <div class="ac-avatar">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                <div class="ac-id-name">{{ $user->name }}</div>
                <div class="ac-id-since">Member since {{ $createdAt }}</div>
                <hr class="ac-id-divider">
                <div style="width:100%;text-align:left;">
                    <div class="ac-id-email">{{ $user->email }}</div>
                    <div class="ac-id-phone">Administrator</div>
                </div>
            </div>
        </div>

        {{-- ── Col 2: Profile Information ── --}}
        <div class="ac-card">
            <div class="ac-card-body">
                <div class="ac-panel-head">
                    <span class="ac-panel-title">Profile Information</span>
                    <button class="ac-edit-btn" type="button" @click="edit('profile')" title="Edit profile">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                    </button>
                </div>

                {{-- Read view --}}
                <div x-show="editing !== 'profile'">
                    <dl class="ac-dl">
                        <div class="ac-dl-row">
                            <dt class="ac-dl-label">Name</dt>
                            <dd class="ac-dl-value">{{ $user->name }}</dd>
                        </div>
                        <div class="ac-dl-row">
                            <dt class="ac-dl-label">Email</dt>
                            <dd class="ac-dl-value">{{ $user->email }}</dd>
                        </div>
                        <div class="ac-dl-row">
                            <dt class="ac-dl-label">Account created</dt>
                            <dd class="ac-dl-value">{{ $createdAt }}</dd>
                        </div>
                    </dl>
                </div>

                {{-- Edit form --}}
                <div x-show="editing === 'profile'" x-cloak>
                    <form method="POST" action="{{ route('admin.account.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="ac-field">
                            <label for="name" class="ac-label">Full Name</label>
                            <input id="name" type="text" name="name"
                                   value="{{ old('name', $user->name) }}"
                                   class="ac-input {{ $errors->has('name') ? 'is-error' : '' }}"
                                   autocomplete="name">
                            @error('name')<p class="ac-field-error">{{ $message }}</p>@enderror
                        </div>

                        <div class="ac-field">
                            <label for="email" class="ac-label">Email Address</label>
                            <input id="email" type="email" name="email"
                                   value="{{ old('email', $user->email) }}"
                                   class="ac-input {{ $errors->has('email') ? 'is-error' : '' }}"
                                   autocomplete="email">
                            @error('email')<p class="ac-field-error">{{ $message }}</p>@enderror
                        </div>

                        <div style="display:flex;gap:.5rem;margin-top:1.1rem;">
                            <button type="submit" class="ac-btn ac-btn-primary">Save Changes</button>
                            <button type="button" class="ac-btn" style="background:#f3f4f6;color:#374151;" @click="editing = null">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ── Col 3: Password + Sessions stacked ── --}}
        <div style="display:flex;flex-direction:column;gap:1rem;">

            {{-- Password panel --}}
            <div class="ac-card">
                <div class="ac-card-body">
                    <div class="ac-panel-head">
                        <span class="ac-panel-title">Change Password</span>
                        <button class="ac-edit-btn" type="button" @click="edit('password')" title="Change password">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Read view --}}
                    <div x-show="editing !== 'password'">
                        <dl class="ac-dl">
                            <div class="ac-dl-row">
                                <dt class="ac-dl-label">Current password</dt>
                                <dd class="ac-dl-value" style="letter-spacing:.1em;">••••••••</dd>
                            </div>
                            <div class="ac-dl-row">
                                <dt class="ac-dl-label">New password</dt>
                                <dd class="ac-dl-value" style="letter-spacing:.1em;">••••••••</dd>
                            </div>
                            <div class="ac-dl-row">
                                <dt class="ac-dl-label">Confirm password</dt>
                                <dd class="ac-dl-value" style="letter-spacing:.1em;">••••••••</dd>
                            </div>
                        </dl>
                        <button type="button" class="ac-btn ac-btn-primary" style="margin-top:1.1rem;" @click="edit('password')">Change Password</button>
                    </div>

                    {{-- Edit form --}}
                    <div x-show="editing === 'password'" x-cloak>
                        <form method="POST" action="{{ route('admin.account.change-password') }}">
                            @csrf
                            @method('PUT')

                            <div class="ac-field" x-data="{show:false}">
                                <label for="current_password" class="ac-label">Current Password</label>
                                <div class="ac-pw-wrap">
                                    <input id="current_password" :type="show?'text':'password'"
                                           name="current_password"
                                           class="ac-input {{ $errors->has('current_password') ? 'is-error' : '' }}"
                                           autocomplete="current-password">
                                    <button type="button" class="ac-eye" @click="show=!show">
                                        <svg x-show="!show" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <svg x-show="show" x-cloak width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                    </button>
                                </div>
                                @error('current_password')<p class="ac-field-error">{{ $message }}</p>@enderror
                            </div>

                            <div class="ac-field" x-data="{show:false}">
                                <label for="password" class="ac-label">New Password</label>
                                <div class="ac-pw-wrap">
                                    <input id="password" :type="show?'text':'password'"
                                           name="password"
                                           class="ac-input {{ $errors->has('password') ? 'is-error' : '' }}"
                                           autocomplete="new-password">
                                    <button type="button" class="ac-eye" @click="show=!show">
                                        <svg x-show="!show" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <svg x-show="show" x-cloak width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                    </button>
                                </div>
                                @error('password')<p class="ac-field-error">{{ $message }}</p>@enderror
                                <p class="ac-field-hint">At least 8 characters — mix of letters &amp; numbers.</p>
                            </div>

                            <div class="ac-field" x-data="{show:false}">
                                <label for="password_confirmation" class="ac-label">Confirm New Password</label>
                                <div class="ac-pw-wrap">
                                    <input id="password_confirmation" :type="show?'text':'password'"
                                           name="password_confirmation"
                                           class="ac-input"
                                           autocomplete="new-password">
                                    <button type="button" class="ac-eye" @click="show=!show">
                                        <svg x-show="!show" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <svg x-show="show" x-cloak width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                    </button>
                                </div>
                            </div>

                            <div style="display:flex;gap:.5rem;margin-top:1rem;">
                                <button type="submit" class="ac-btn ac-btn-primary">Change Password</button>
                                <button type="button" class="ac-btn" style="background:#f3f4f6;color:#374151;" @click="editing = null">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Active Sessions panel --}}
            <div class="ac-card">
                <div class="ac-card-body">
                    <div class="ac-panel-head">
                        <span class="ac-panel-title">Active Sessions</span>
                        <span class="ac-manage-badge">Active</span>
                    </div>
                    <div>
                        <div class="ac-session-item">
                            <div>
                                <div class="ac-session-name">Current Session</div>
                                <div class="ac-session-meta">{{ request()->ip() }} · now</div>
                            </div>
                            <span style="font-size:.65rem;background:#f0fdf4;color:#15803d;border-radius:999px;padding:.15rem .55rem;font-weight:600;">Current</span>
                        </div>
                        <div class="ac-session-item">
                            <div>
                                <div class="ac-session-name">Browser Session</div>
                                <div class="ac-session-meta">Admin Panel · authenticated</div>
                            </div>
                            <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Log out from this session?')">
                                @csrf
                                <button type="submit" class="ac-session-del" title="Sign out">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Full-width bottom row: Activity tabs ── --}}
        <div class="ac-card ac-col-span">
            <div class="ac-tabs">
                <button class="ac-tab" :class="{ active: activeTab === 'activity' }" @click="activeTab = 'activity'">Recent Activity</button>
                <button class="ac-tab" :class="{ active: activeTab === 'login' }"    @click="activeTab = 'login'">Login History</button>
               </div>

            {{-- Recent Activity --}}
            <div x-show="activeTab === 'activity'" style="padding:.5rem;">
                <div class="ac-activity-row">
                    <div class="ac-activity-icon">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div style="flex:1;">
                        <div class="ac-activity-title">Signed in to Admin Panel</div>
                        <div class="ac-activity-meta">Today · {{ now()->format('g:i A') }}</div>
                    </div>
                    <span class="ac-status-badge ac-status-success">Success</span>
                </div>
                <div class="ac-activity-row">
                    <div class="ac-activity-icon">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div style="flex:1;">
                        <div class="ac-activity-title">Profile last updated</div>
                        <div class="ac-activity-meta">{{ $user->updated_at ? $user->updated_at->format('M j, Y · g:i A') : '—' }}</div>
                    </div>
                    <span class="ac-status-badge ac-status-success">Success</span>
                </div>
                <div class="ac-activity-row">
                    <div class="ac-activity-icon">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <div style="flex:1;">
                        <div class="ac-activity-title">Account created</div>
                        <div class="ac-activity-meta">{{ $createdAt }}</div>
                    </div>
                    <span class="ac-status-badge ac-status-success">Success</span>
                </div>
            </div>

            {{-- Login History --}}
            <div x-show="activeTab === 'login'" x-cloak style="padding:.5rem;">
                <div class="ac-activity-row">
                    <div class="ac-activity-icon">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    </div>
                    <div style="flex:1;">
                        <div class="ac-activity-title">Admin Panel Login</div>
                        <div class="ac-activity-meta">{{ request()->ip() }} · {{ now()->format('M j, Y g:i A') }}</div>
                    </div>
                    <span class="ac-status-badge ac-status-success">Active</span>
                </div>
            </div>

            {{-- Connected Apps --}}
            <div x-show="activeTab === 'apps'" x-cloak style="padding:1.25rem 1rem;">
                <div style="text-align:center;padding:1.5rem 0;">
                    <svg style="margin:0 auto .5rem;color:#d1d5db;" width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                    <p style="font-size:.8rem;color:#9ca3af;">No connected apps yet.</p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
