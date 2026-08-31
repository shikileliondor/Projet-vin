import { Head, Link, usePage } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { PageHeader } from '@/components/page-header';
import { Pagination } from '@/components/pagination';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { create, index, show } from '@/routes/inventories';
import type { Paginated } from '@/types';

type Inventory = {
    id: number;
    reference: string;
    user: string;
    items_count: number;
    note: string | null;
    completed_at: string;
};

export default function InventoriesIndex({
    inventories,
}: {
    inventories: Paginated<Inventory>;
}) {
    const { permissions } = usePage().props;
    return (
        <>
            <Head title="Inventaires" />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <PageHeader
                    title="Inventaires"
                    description="Historique des comptages physiques"
                    actions={
                        permissions?.manageInventories ? (
                            <Button asChild size="lg">
                                <Link href={create()}>
                                    <Plus /> Nouvel inventaire
                                </Link>
                            </Button>
                        ) : undefined
                    }
                />
                <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    {inventories.data.map((inventory) => (
                        <Card key={inventory.id} className="py-5">
                            <CardContent className="grid gap-3">
                                <div>
                                    <h2 className="font-semibold">
                                        {inventory.reference}
                                    </h2>
                                    <p className="text-muted-foreground text-sm">
                                        {inventory.completed_at}
                                    </p>
                                </div>
                                <p>
                                    <strong>{inventory.items_count}</strong>{' '}
                                    produit(s) compté(s)
                                </p>
                                <p className="text-muted-foreground text-sm">
                                    Par {inventory.user}
                                </p>
                                <Button asChild variant="outline">
                                    <Link href={show(inventory.id)}>
                                        Voir les écarts
                                    </Link>
                                </Button>
                            </CardContent>
                        </Card>
                    ))}
                </div>
                {inventories.data.length === 0 && (
                    <p className="text-muted-foreground rounded-lg border py-12 text-center">
                        Aucun inventaire réalisé.
                    </p>
                )}
                <Pagination links={inventories.links} />
            </div>
        </>
    );
}

InventoriesIndex.layout = {
    breadcrumbs: [{ title: 'Inventaires', href: index() }],
};
