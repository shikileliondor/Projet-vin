import { Head, Link } from '@inertiajs/react';
import {
    AlertTriangle,
    ArrowDownToLine,
    ArrowUpFromLine,
    Boxes,
} from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { dashboard } from '@/routes';
import { index as stockIndex } from '@/routes/stock';
import { create as createEntry } from '@/routes/stock/entries';
import { create as createExit } from '@/routes/stock/exits';

export default function Dashboard({
    stats,
    permissions,
}: {
    stats: {
        totalBottles: number;
        totalProducts: number;
        lowStock: number;
        outOfStock: number;
    };
    permissions: { recordEntry: boolean; recordExit: boolean };
}) {
    return (
        <>
            <Head title="Accueil" />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <section className="grid gap-4">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">
                            Accueil
                        </h1>
                        <p className="text-muted-foreground text-sm">
                            Actions rapides
                        </p>
                    </div>

                    <div className="grid gap-3 sm:grid-cols-3">
                        {permissions.recordEntry && (
                            <Button
                                asChild
                                size="lg"
                                className="h-16 text-base"
                            >
                                <Link href={createEntry()}>
                                    <ArrowDownToLine /> Entrée stock
                                </Link>
                            </Button>
                        )}
                        {permissions.recordExit && (
                            <Button
                                asChild
                                size="lg"
                                variant="outline"
                                className="h-16 text-base"
                            >
                                <Link href={createExit()}>
                                    <ArrowUpFromLine /> Sortie stock
                                </Link>
                            </Button>
                        )}
                        <Button
                            asChild
                            size="lg"
                            variant="secondary"
                            className="h-16 text-base"
                        >
                            <Link href={stockIndex()}>
                                <Boxes /> Voir le stock
                            </Link>
                        </Button>
                    </div>
                </section>

                <section className="grid gap-3 sm:grid-cols-3">
                    <SimpleStat
                        label="Bouteilles"
                        value={stats.totalBottles}
                        tone="normal"
                    />
                    <SimpleStat
                        label="Stock faible"
                        value={stats.lowStock}
                        tone={stats.lowStock > 0 ? 'warning' : 'normal'}
                    />
                    <SimpleStat
                        label="Ruptures"
                        value={stats.outOfStock}
                        tone={stats.outOfStock > 0 ? 'danger' : 'normal'}
                    />
                </section>

                {(stats.lowStock > 0 || stats.outOfStock > 0) && (
                    <Card>
                        <CardContent className="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between">
                            <div className="flex items-center gap-3">
                                <AlertTriangle className="size-5 text-amber-600" />
                                <p className="font-medium">
                                    Certains produits demandent votre attention.
                                </p>
                            </div>
                            <Button asChild variant="outline">
                                <Link
                                    href={stockIndex({
                                        query: { level: 'low' },
                                    })}
                                >
                                    Ouvrir le stock
                                </Link>
                            </Button>
                        </CardContent>
                    </Card>
                )}
            </div>
        </>
    );
}

function SimpleStat({
    label,
    value,
    tone,
}: {
    label: string;
    value: number;
    tone: 'normal' | 'warning' | 'danger';
}) {
    const toneClass = {
        normal: 'text-stone-950',
        warning: 'text-amber-700',
        danger: 'text-red-700',
    }[tone];

    return (
        <Card className="py-5">
            <CardContent>
                <p className="text-muted-foreground text-sm">{label}</p>
                <p className={`mt-2 text-4xl font-bold ${toneClass}`}>
                    {value}
                </p>
            </CardContent>
        </Card>
    );
}

Dashboard.layout = { breadcrumbs: [{ title: 'Accueil', href: dashboard() }] };
