import type { AuthLayoutProps } from '@/types';

export default function AuthSimpleLayout({ children }: AuthLayoutProps) {
    return (
        <div className="bg-background flex min-h-svh items-center justify-center px-5 py-8">
            <div className="w-full max-w-sm">{children}</div>
        </div>
    );
}
