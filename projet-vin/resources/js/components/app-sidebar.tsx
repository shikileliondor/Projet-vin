import { Link, usePage } from '@inertiajs/react';
import {
    Boxes,
    ClipboardCheck,
    History,
    LayoutDashboard,
    Package,
    Settings,
    Users,
} from 'lucide-react';
import AppLogo from '@/components/app-logo';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
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
import { index as inventoriesIndex } from '@/routes/inventories';
import { index as productsIndex } from '@/routes/products';
import { edit as settingsEdit } from '@/routes/settings/application';
import { index as stockIndex } from '@/routes/stock';
import { index as movementsIndex } from '@/routes/stock/movements';
import { index as usersIndex } from '@/routes/users';
import type { NavItem } from '@/types';

export function AppSidebar() {
    const { permissions } = usePage().props;
    const mainNavItems: NavItem[] = [
        { title: 'Accueil', href: dashboard(), icon: LayoutDashboard },
        { title: 'Produits', href: productsIndex(), icon: Package },
        { title: 'Stock', href: stockIndex(), icon: Boxes },
        ...(permissions?.viewInventories
            ? [
                  {
                      title: 'Inventaire',
                      href: inventoriesIndex(),
                      icon: ClipboardCheck,
                  },
              ]
            : []),
        ...(permissions?.viewMovements
            ? [{ title: 'Mouvements', href: movementsIndex(), icon: History }]
            : []),
    ];
    const adminNavItems: NavItem[] = permissions?.admin
        ? [
              { title: 'Utilisateurs', href: usersIndex(), icon: Users },
              { title: 'Paramètres', href: settingsEdit(), icon: Settings },
          ]
        : [];

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>
            <SidebarContent>
                <NavMain items={mainNavItems} label="Gestion du stock" />
                {adminNavItems.length > 0 && (
                    <NavMain items={adminNavItems} label="Administration" />
                )}
            </SidebarContent>
            <SidebarFooter>
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
