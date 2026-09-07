import type { AuthLayoutProps } from '@/types';

export default function AuthSimpleLayout({ children }: AuthLayoutProps) {
    return (
        <div className="flex min-h-svh items-center justify-center bg-white px-5 py-8 text-stone-900">
            <div className="w-full max-w-sm">{children}</div>
        </div>
    );
}
