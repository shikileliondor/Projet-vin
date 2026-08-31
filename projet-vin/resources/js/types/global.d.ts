import type { Auth } from '@/types/auth';

declare module 'react' {
    interface InputHTMLAttributes<T> {
        passwordrules?: string;
    }
}

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            name: string;
            currency: string;
            auth: Auth;
            sidebarOpen: boolean;
            flash: { success?: string };
            permissions: {
                manageProducts: boolean;
                recordEntries: boolean;
                recordExits: boolean;
                manageInventories: boolean;
                viewInventories: boolean;
                viewMovements: boolean;
                admin: boolean;
            } | null;
            [key: string]: unknown;
        };
    }
}
