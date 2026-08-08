@extends('layouts.app')

@section('title', 'Role & Permission Manager')
@section('header', 'Enterprise RBAC & Permission Matrix')
@section('subheader', 'Configure Security Roles, Edit Granular Capabilities, and Assign User Permissions')

@section('content')
<div class="space-y-8" x-data="{ newRoleModal: false, editRoleModal: false, selectedRole: null, selectedRoleName: '', selectedRoleDisplay: '', selectedRolePerms: [] }">
    
    <!-- Top Action Bar -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <span class="px-3.5 py-1.5 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 font-bold text-xs">
            {{ $roles->count() }} Security Roles Configured
        </span>
        <div class="flex items-center space-x-3">
            <button @click="newRoleModal = true" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-lg shadow-indigo-600/30 transition flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                + Create Custom Role
            </button>
        </div>
    </div>

    <!-- Active Roles Quick Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach($roles as $role)
            <div class="glass-card p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-extrabold uppercase {{ $role->name === 'super-admin' ? 'bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                            {{ $role->name }}
                        </span>
                        @if(!in_array($role->name, ['super-admin', 'admin', 'teacher', 'student', 'parent']))
                            <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('Delete role {{ $role->display_name }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-500 hover:text-rose-700 text-xs font-bold">&times;</button>
                            </form>
                        @endif
                    </div>
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white">{{ $role->display_name }}</h4>
                    <p class="text-[11px] text-slate-400 mt-1">
                        {{ $role->name === 'super-admin' ? 'All System Capabilities' : $role->permissions->count() . ' Granular Permissions' }}
                    </p>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                    <span class="text-slate-400 font-mono text-[10px]">{{ $role->users->count() }} Users</span>
                    @if($role->name !== 'super-admin')
                        <button type="button" @click="editRoleModal = true; selectedRole = {{ $role->id }}; selectedRoleDisplay = '{{ $role->display_name }}'; selectedRolePerms = {{ json_encode($role->permissions->pluck('id')) }};" 
                                class="text-indigo-600 dark:text-indigo-400 hover:underline font-bold text-[11px]">
                            Edit Role &rarr;
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Interactive Role-Permission Matrix -->
    <div class="flat-table-wrapper">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800/80 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Security Capability Matrix</h3>
                <p class="text-xs text-slate-500">Check or uncheck capabilities and click "Save" for that role</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50/80 dark:bg-slate-800/40 text-slate-400 uppercase text-[10px] font-bold">
                    <tr>
                        <th class="px-5 py-4 min-w-[200px]">Permission Capability</th>
                        @foreach($roles as $role)
                            <th class="px-5 py-4 text-center min-w-[140px]">
                                <span class="block text-slate-800 dark:text-slate-200 font-bold text-xs">{{ $role->display_name }}</span>
                                <span class="text-[9px] font-mono text-slate-400 block mb-1">({{ $role->name }})</span>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($permissions as $group => $perms)
                        <tr class="bg-indigo-50/40 dark:bg-indigo-950/20">
                            <td colspan="{{ $roles->count() + 1 }}" class="px-5 py-2 font-extrabold uppercase text-[10px] tracking-wider text-indigo-700 dark:text-indigo-300">
                                🛡️ {{ strtoupper($group) }} SUBSYSTEM CAPABILITIES
                            </td>
                        </tr>
                        @foreach($perms as $perm)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30">
                                <td class="px-5 py-3 font-semibold text-slate-900 dark:text-white">
                                    {{ $perm->display_name }}
                                    <span class="block text-[10px] text-slate-400 font-mono">{{ $perm->name }}</span>
                                </td>
                                @foreach($roles as $role)
                                    <td class="px-5 py-3 text-center">
                                        @if($role->name === 'super-admin')
                                            <span class="text-emerald-500 font-bold text-xs">✓ Full Pass</span>
                                        @else
                                            <form method="POST" action="{{ route('admin.roles.permissions', $role) }}" class="inline" id="form-{{ $role->id }}-{{ $perm->id }}">
                                                @csrf
                                                @php
                                                    $has = $role->permissions->contains('id', $perm->id);
                                                    $otherPermIds = $role->permissions->where('id', '!=', $perm->id)->pluck('id')->toArray();
                                                @endphp
                                                @foreach($otherPermIds as $opId)
                                                    <input type="hidden" name="permissions[]" value="{{ $opId }}">
                                                @endforeach
                                                @if(!$has)
                                                    <input type="hidden" name="permissions[]" value="{{ $perm->id }}">
                                                @endif
                                                <button type="submit" title="{{ $has ? 'Click to Revoke' : 'Click to Grant' }}" 
                                                        class="w-6 h-6 rounded-lg font-bold text-xs inline-flex items-center justify-center transition {{ $has ? 'bg-emerald-500 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-800 text-slate-400 hover:bg-indigo-100 hover:text-indigo-600' }}">
                                                    {{ $has ? '✓' : '+' }}
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- User Role Assignment Card -->
    <div class="glass-card p-6">
        <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-4">Quick User Role Assignment Desk</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($users as $u)
                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-700/60 flex items-center justify-between">
                    <div class="min-w-0 mr-2">
                        <p class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ $u->name }}</p>
                        <p class="text-[11px] text-slate-400 truncate">{{ $u->email }}</p>
                        <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300">
                            {{ $u->primary_role }}
                        </span>
                    </div>
                    <form method="POST" action="{{ route('admin.roles.assign-user', $u) }}" class="flex items-center space-x-1">
                        @csrf
                        <select name="role_id" onchange="this.form.submit()" class="px-2 py-1.5 rounded-xl bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-[11px] font-bold">
                            @foreach($roles as $r)
                                <option value="{{ $r->id }}" {{ $u->hasRole($r->name) ? 'selected' : '' }}>{{ $r->display_name }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            @endforeach
        </div>
    </div>

    <!-- ================= MODAL: CREATE CUSTOM ROLE ================= -->
    <div x-show="newRoleModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        <div @click.away="newRoleModal = false" class="glass-card w-full max-w-xl p-6 bg-white dark:bg-slate-900 shadow-2xl max-h-[90vh] overflow-y-auto">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Create Custom Security Role</h3>
            <p class="text-xs text-slate-500 mb-4">Define a new security tier and choose its granted capabilities.</p>
            
            <form method="POST" action="{{ route('admin.roles.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Role Title / Display Name</label>
                    <input type="text" name="display_name" required placeholder="e.g. Examination Officer" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-2">Grant Initial Capabilities</label>
                    <div class="space-y-3 max-h-60 overflow-y-auto p-3 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                        @foreach($permissions as $grp => $grpPerms)
                            <div>
                                <p class="text-[10px] font-extrabold uppercase text-indigo-600 dark:text-indigo-400 mb-1">{{ $grp }} Subsystem</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                                    @foreach($grpPerms as $gp)
                                        <label class="flex items-center space-x-2 text-xs font-medium text-slate-700 dark:text-slate-300 cursor-pointer">
                                            <input type="checkbox" name="permissions[]" value="{{ $gp->id }}" class="rounded text-indigo-600 border-slate-300">
                                            <span>{{ $gp->display_name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="newRoleModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold shadow-md">Create Role</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= MODAL: EDIT ROLE ================= -->
    <div x-show="editRoleModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        <div @click.away="editRoleModal = false" class="glass-card w-full max-w-xl p-6 bg-white dark:bg-slate-900 shadow-2xl max-h-[90vh] overflow-y-auto">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Edit Role & Permissions</h3>
            <p class="text-xs text-slate-500 mb-4">Modify role display title and batch update capabilities.</p>
            
            <form :action="'/admin/roles/' + selectedRole" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Role Title / Display Name</label>
                    <input type="text" name="display_name" x-model="selectedRoleDisplay" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-2">Configure Capabilities</label>
                    <div class="space-y-3 max-h-60 overflow-y-auto p-3 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                        @foreach($permissions as $grp => $grpPerms)
                            <div>
                                <p class="text-[10px] font-extrabold uppercase text-indigo-600 dark:text-indigo-400 mb-1">{{ $grp }} Subsystem</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                                    @foreach($grpPerms as $gp)
                                        <label class="flex items-center space-x-2 text-xs font-medium text-slate-700 dark:text-slate-300 cursor-pointer">
                                            <input type="checkbox" name="permissions[]" value="{{ $gp->id }}" :checked="selectedRolePerms.includes({{ $gp->id }})" class="rounded text-indigo-600 border-slate-300">
                                            <span>{{ $gp->display_name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="editRoleModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold shadow-md">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
