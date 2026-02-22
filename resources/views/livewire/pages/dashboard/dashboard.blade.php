<x-layouts::app :title="__('Dashboard')">
    <div class="flex flex-col gap-6 w-full h-full">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-textDark">{{ __('Dashboard') }}</h1>
                <p class="text-sm text-textGray font-medium mt-1">Welcome back, {{ auth()->user()->name }}!</p>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Total Users -->
            <div class="bg-white rounded-2xl border border-inputBorder p-6 flex flex-col justify-between shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-textGray text-sm font-semibold uppercase tracking-wider">Total Users</span>
                    <div class="p-2 bg-primaryLight/20 text-primary rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-3xl font-bold text-textDark">{{\App\Models\User::count()}}</span>
                    <span class="text-xs text-primary font-medium ml-2">+12% this month</span>
                </div>
            </div>

            <!-- Active Rentals -->
            <div class="bg-white rounded-2xl border border-inputBorder p-6 flex flex-col justify-between shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-textGray text-sm font-semibold uppercase tracking-wider">Active Rentals</span>
                    <div class="p-2 bg-accentYellow/20 text-accentYellow rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-3xl font-bold text-textDark">0</span>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="bg-white rounded-2xl border border-inputBorder p-6 flex flex-col justify-between shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-textGray text-sm font-semibold uppercase tracking-wider">Total Revenue</span>
                    <div class="p-2 bg-primary/20 text-primaryDark rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-3xl font-bold text-textDark">Rp 0</span>
                </div>
            </div>
        </div>

        <!-- Recent Activity Blank Slate -->
        <div
            class="bg-white rounded-2xl border border-inputBorder flex-1 min-h-[300px] flex items-center justify-center">
            <div class="text-center">
                <div
                    class="w-16 h-16 bg-backgroundSoft rounded-full border border-inputBorder flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-8 h-8 text-textGray">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-textDark mb-1">No Recent Activity</h3>
                <p class="text-textGray text-sm">Activities like new bookings and users will appear here.</p>
            </div>
        </div>
    </div>
</x-layouts::app>