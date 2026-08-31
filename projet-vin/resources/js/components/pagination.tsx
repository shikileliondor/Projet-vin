import { Link } from '@inertiajs/react';
import type { PaginationLink } from '@/types';

export function Pagination({ links }: { links: PaginationLink[] }) {
    if (links.length <= 3) return null;

    return (
        <nav
            className="flex flex-wrap justify-center gap-2"
            aria-label="Pagination"
        >
            {links.map((link, index) => (
                <Link
                    key={`${link.label}-${index}`}
                    href={link.url ?? '#'}
                    preserveScroll
                    className={`min-w-10 rounded-md border px-3 py-2 text-center text-sm ${
                        link.active
                            ? 'bg-primary text-primary-foreground'
                            : 'bg-background'
                    } ${!link.url ? 'pointer-events-none opacity-50' : ''}`}
                    dangerouslySetInnerHTML={{ __html: link.label }}
                />
            ))}
        </nav>
    );
}
