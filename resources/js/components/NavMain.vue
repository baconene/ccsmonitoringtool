<script setup lang="ts">
import {
  SidebarGroup,
  SidebarGroupLabel,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem
} from '@/components/ui/sidebar'
import { type NavItem } from '@/types'
import { Link, usePage } from '@inertiajs/vue3'
import { ref, computed, inject } from 'vue'

defineProps<{ items: NavItem[] }>()

const page = usePage()
const currentUrl = computed(() => page.url)
const isSidebarCollapsed = inject('isSidebarCollapsed', ref(false))

const openMenus = ref<string[]>([])
const hoveredMenu = ref<string | null>(null)
const hoverTarget = ref<HTMLElement | null>(null)

function toggleMenu(title: string) {
  if (openMenus.value.includes(title)) {
    openMenus.value = openMenus.value.filter(t => t !== title)
  } else {
    openMenus.value.push(title)
  }
}

function onMouseEnter(itemTitle: string, event: MouseEvent) {
  hoveredMenu.value = itemTitle
  hoverTarget.value = event.currentTarget as HTMLElement
}

function onMouseLeave() {
  hoveredMenu.value = null
  hoverTarget.value = null
}
</script>

<template>
  <SidebarGroup class="px-2 py-0">
    <SidebarGroupLabel>Platform</SidebarGroupLabel>
    <SidebarMenu>
      <template v-for="item in items" :key="item.title">
        <SidebarMenuItem v-if="item.children">
          <div
            class="relative group"
            @mouseenter="isSidebarCollapsed ? onMouseEnter(item.title, $event) : null"
            @mouseleave="isSidebarCollapsed ? onMouseLeave() : null"
          >
            <SidebarMenuButton
              as-child
              :tooltip="isSidebarCollapsed ? item.title : undefined"
              @click="!isSidebarCollapsed && toggleMenu(item.title)"
            >
              <div class="flex items-center gap-2 w-full cursor-pointer">
                <component :is="item.icon" class="w-4 h-4" />
                <span v-if="!isSidebarCollapsed">{{ item.title }}</span>
              </div>
            </SidebarMenuButton>

            <!-- Expanded children inline -->
            <SidebarMenu
              v-if="!isSidebarCollapsed && openMenus.includes(item.title)"
              class="ml-6 mt-1 space-y-1"
            >
              <SidebarMenuItem
                v-for="child in item.children"
                :key="child.title"
              >
                <SidebarMenuButton as-child :is-active="child.href === currentUrl">
                  <Link :href="child.href" class="text-sm px-2 py-1 hover:bg-muted rounded">
                    {{ child.title }}
                  </Link>
                </SidebarMenuButton>
              </SidebarMenuItem>
            </SidebarMenu>
          </div>
        </SidebarMenuItem>

        <!-- Regular menu -->
        <SidebarMenuItem v-else>
          <SidebarMenuButton
            as-child
            :is-active="item.href === currentUrl"
            :tooltip="item.title"
          >
            <Link :href="item.href" class="flex items-center gap-2">
              <component :is="item.icon" class="w-4 h-4" />
              <span v-if="!isSidebarCollapsed">{{ item.title }}</span>
            </Link>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </template>
    </SidebarMenu>
  </SidebarGroup>

  <!-- Flyout (teleported) -->
  <teleport to="body">
    <div
      v-if="isSidebarCollapsed && hoveredMenu && hoverTarget"
      :style="{
        position: 'fixed',
        top: `${hoverTarget.getBoundingClientRect().top}px`,
        left: `${hoverTarget.getBoundingClientRect().right + 4}px`
      }"
      class=" absolute z-50 z-[9999] w-48 rounded-md bg-white dark:bg-gray-800 shadow-xl border border-gray-200 dark:border-gray-700"
      @mouseenter="hoveredMenu = hoveredMenu"
      @mouseleave="onMouseLeave"
    >
      <SidebarMenu class="py-2">
        <SidebarMenuItem
          v-for="child in items.find(i => i.title === hoveredMenu)?.children || []"
          :key="child.title"
        >
          <SidebarMenuButton as-child :is-active="child.href === currentUrl">
            <Link :href="child.href" class="block px-3 py-2 text-sm hover:bg-muted rounded">
              {{ child.title }}
            </Link>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </div>
  </teleport>
</template>
