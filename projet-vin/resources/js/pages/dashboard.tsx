import { Head, Link } from '@inertiajs/react';
import {
    AlertTriangle,
    ArrowDownToLine,
    ArrowUpFromLine,
    Boxes,
    Package,
    PackageX,
} from 'lucide-react';
import { PageHeader } from '@/components/page-header';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { dashboard } from '@/routes';
import { create as createEntry } from '@/routes/stock/entries';
import { create as createExit } from '@/routes/stock/exits';

type Movement = {
    id: number;
    product: string;
    user: string;
    type: string;
    type_label: string;
    quantity: number;
    stock_after: number;
    created_at: string;
};

export default function Dashboard({
    stats,
    recentMovements,
    permissions,
}: {
    stats: {
        totalBottles: number;
        totalProducts: number;
        lowStock: number;
        outOfStock: number;
    };
    recentMovements: Movement[];
    permissions: { recordEntry: boolean; recordExit: boolean };
}) {
    const cards = [
        {
            label: 'Bouteilles en stock',
            value: stats.totalBottles,
            icon: Boxes,
        },
        {
            label: 'Produits actifs',
            value: stats.totalProducts,
            icon: Package,
        },
        {
            label: 'Stocks faibles',
            value: stats.lowStock,
            icon: AlertTriangle,
        },
        {
            label: 'Ruptures',
            value: stats.outOfStock,
            icon: PackageX,
        },
    ];

    return (
        <>
            <Head title="Accueil" />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <PageHeader
                    title="Accueil"
                    description="Vue rapide de votre stock"
                    actions={
                        <>
                            {permissions.recordEntry && (
                                <Button asChild size="lg">
                                    <Link href={createEntry()}>
                                        <ArrowDownToLine /> Entrée
                                    </Link>
                                </Button>
                            )}
                            {permissions.recordExit && (
                                <Button asChild size="lg" variant="outline">
                                    <Link href={createExit()}>
                                        <ArrowUpFromLine /> Sortie
                                    </Link>
                                </Button>
                            )}
                        </>
                    }
                />

                <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    {cards.map(({ label, value, icon: Icon }) => (
                        <Card key={label} className="gap-3 py-5">
                            <CardHeader className="flex-row items-center justify-between">
                                <CardTitle className="text-sm font-medium">
                                    {label}
                                </CardTitle>
                                <Icon className="text-gold size-5" />
                            </CardHeader>
                            <CardContent>
                                <p className="text-3xl font-bold">{value}</p>
                            </CardContent>
                        </Card>
                    ))}
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Derniers mouvements</CardTitle>
                    </CardHeader>
                    <CardContent className="grid gap-3">
                        {recentMovements.length === 0 && (
                            <p className="text-muted-foreground py-8 text-center">
                                Aucun mouvement enregistré.
                            </p>
                        )}
                        {recentMovements.map((movement) => (
                            <div
                                key={movement.id}
                                className="flex flex-col gap-2 rounded-lg border p-3 sm:flex-row sm:items-center sm:justify-between"
                            >
                                <div>
                                    <p className="font-medium">
                                        {movement.product}
                                    </p>
                                    <p className="text-muted-foreground text-sm">
                                        {movement.user} · {movement.created_at}
                                    </p>
                                </div>
                                <div className="flex items-center gap-3">
                                    <Badge variant="outline">
                                        {movement.type_label}
                                    </Badge>
                                    <span
                                        className={`font-bold ${movement.quantity >= 0 ? 'text-emerald-700' : 'text-red-700'}`}
                                    >
                                        {movement.quantity > 0 ? '+' : ''}
                                        {movement.quantity}
                                    </span>
                                    <span className="text-muted-foreground text-sm">
                                        Stock : {movement.stock_after}
                                    </span>
                                </div>
                            </div>
                        ))}
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

Dashboard.layout = { breadcrumbs: [{ title: 'Accueil', href: dashboard() }] };
