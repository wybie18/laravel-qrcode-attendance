<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { BookCheck, BookOpen, Briefcase, Building, Building2, FolderGit2, LayoutGrid, Users } from 'lucide-vue-next';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { index as attendanceLogIndex } from '@/routes/attendance-logs';
import { index as officeIndex } from '@/routes/offices';
import { index as personnelIndex } from '@/routes/personnels';
import { index as positionIndex } from '@/routes/positions'
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Attendance',
        href: attendanceLogIndex(),
        icon: BookCheck,
    },
    {
        title: 'Personnel',
        href: personnelIndex(),
        icon: Users,
    },
    {
        title: 'Offices',
        href: officeIndex(),
        icon: Building2,
    },
    {
        title: 'Positions',
        href: positionIndex(),
        icon: Briefcase,
    }
];

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/wybie18/laravel-qrcode-attendance',
        icon: FolderGit2,
    }
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
