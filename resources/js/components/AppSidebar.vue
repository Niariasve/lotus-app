<script setup lang="ts">
    import { Link } from '@inertiajs/vue3';
    import { Apple, BookOpen, BookUser, Container, Folder, LayoutGrid, User } from 'lucide-vue-next';
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
    import contactPlatforms from '@/routes/contact-platforms';
    import customers from '@/routes/customers';
    import products from '@/routes/products';
    import suppliers from '@/routes/suppliers';
    import { type NavItem } from '@/types';
    import AppLogo from './AppLogo.vue';

    const mainNavItems: NavItem[] = [
        {
            title: 'Dashboard',
            href: dashboard(),
            icon: LayoutGrid,
        },
        {
            title: 'Customers',
            href: customers.index(),
            icon: User,
        },
        {
            title: 'Contact Platforms',
            href: contactPlatforms.index(),
            icon: BookUser,
        },
        {
            title: 'Products',
            href: products.index(),
            icon: Apple,
        },
        {
            title: 'Suppliers',
            href: suppliers.index(),
            icon: Container,
        }
    ];

    const footerNavItems: NavItem[] = [
        {
            title: 'Github Repo',
            href: 'https://github.com/laravel/vue-starter-kit',
            icon: Folder,
        },
        {
            title: 'Documentation',
            href: 'https://laravel.com/docs/starter-kits#vue',
            icon: BookOpen,
        },
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
