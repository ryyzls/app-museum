@extends('layouts.admin')

@section('title', 'Users - Alphaseum')
@section('breadcrumb', 'Users')
@section('page-title', 'Users')

@section('content')

    <div class="admin-card p-8">

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead class="border-b border-gray-100">

                    <tr class="text-left">

                        <th class="px-6 py-5 text-xs uppercase tracking-[0.3em] text-gray-400">
                            Name
                        </th>

                        <th class="px-6 py-5 text-xs uppercase tracking-[0.3em] text-gray-400">
                            Email
                        </th>

                        <th class="px-6 py-5 text-xs uppercase tracking-[0.3em] text-gray-400">
                            Role
                        </th>

                        <th class="px-6 py-5 text-xs uppercase tracking-[0.3em] text-gray-400">
                            Transactions
                        </th>

                        <th class="px-6 py-5 text-xs uppercase tracking-[0.3em] text-gray-400">
                            Reviews
                        </th>

                        <th class="px-6 py-5 text-xs uppercase tracking-[0.3em] text-gray-400">
                            Action
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @foreach($users as $user)

                        <tr class="border-b border-gray-100 hover:bg-black/5 transition">

                            <td class="px-6 py-5">
                                {{ $user->name }}
                            </td>

                            <td class="px-6 py-5">
                                {{ $user->email }}
                            </td>

                            <td class="px-6 py-5">
                                {{ ucfirst($user->role) }}
                            </td>

                            <td class="px-6 py-5">
                                {{ $user->transactions_count }}
                            </td>

                            <td class="px-6 py-5">
                                {{ $user->reviews_count }}
                            </td>

                            <td class="px-6 py-5">

                                <a href="{{ route('admin.users.show', $user) }}" class="text-[#c9a96e] hover:underline">

                                    View

                                </a>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

@endsection