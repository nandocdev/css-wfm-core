<div class="mt-auto px-6 py-4 border-t border-gray-100 dark:border-gray-800">
    <div x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="text-center">
        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">
            Contact Center &copy; {{ date('Y') }}
        </p>
        <p class="mt-1 text-[10px] text-gray-400 dark:text-gray-500">
            v1.0.0 &bull; WorkForce Core
        </p>
    </div>
    
    <div x-show="!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen"
         class="text-center opacity-40">
        <span class="text-[10px] font-bold text-gray-400">WFM</span>
    </div>
</div>
