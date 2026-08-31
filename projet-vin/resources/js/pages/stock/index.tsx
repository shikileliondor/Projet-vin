import { Form, Head, Link, usePage } from '@inertiajs/react';
import { ArrowDownToLine, ArrowUpFromLine, History } from 'lucide-react';
import { PageHeader } from '@/components/page-header';
import { StockStatus } from '@/components/stock-status';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { index } from '@/routes/stock';
import { create as createEntry } from '@/routes/stock/entries';
import { create as createExit } from '@/routes/stock/exits';
import { index as movementsIndex } from '@/routes/stock/movements';

type StockProduct = {
    id: number;
    name: string;
    brand: string | null;
    category: string;
    stock_quantity: number;
    minimum_stock: number;
    bottles_per_case: number;
    stock_status: 'normal' | 'low' | 'out';
    stock_display: string;
};

export default function StockIndex({
    products,
    filters,
}: {
    products: StockProduct[];
    filters: { search?: string; level?: string };
}) {
    const { permissions } = usePage().props;

    return (
        <>
            <Head title="Stock" />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <PageHeader
                    title="Stock"
                    description={`${products.length} produit(s) affiché(s)`}
                    actions={
                        <>
                            {permissions?.recordEntries && (
                                <Button asChild size="lg">
                                    <Link href={createEntry()}>
                                        <ArrowDownToLine /> Entrée
                                    </Link>
                                </Button>
                            )}
                            <Button asChild size="lg" variant="outline">
                                <Link href={createExit()}>
                                    <ArrowUpFromLine /> Sortie
                                </Link>
                            </Button>
                            {permissions?.viewMovements && (
                                <Button asChild size="lg" variant="ghost">
                                    <Link href={movementsIndex()}>
                                        <History /> Historique
                                    </Link>
                                </Button>
                            )}
                        </>
                    }
                />
                <Card className="py-4">
                    <CardContent>
                        <Form
                            {...index.form()}
                            className="grid gap-3 sm:grid-cols-[1fr_200px_auto]"
                        >
                            <Input
                                name="search"
                                defaultValue={filters.search}
                                placeholder="Rechercher un produit"
                                className="h-11"
                            />
                            <select
                                name="level"
                                defaultValue={filters.level ?? ''}
                                className="border-input bg-background h-11 rounded-md border px-3"
                            >
                                <option value="">Tous les stocks</option>
                                <option value="low">Stocks faibles</option>
                                <option value="out">Ruptures</option>
                            </select>
                            <Button
                                type="submit"
                                variant="outline"
                                className="h-11"
                            >
                                Filtrer
                            </Button>
                        </Form>
                    </CardContent>
                </Card>
                <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    {products.map((product) => (
                        <Card key={product.id} className="py-5">
                            <CardContent className="grid gap-4">
                                <div className="flex items-start justify-between gap-3">
                                    <div>
                                        <h2 className="font-semibold">
                                            {product.name}
                                        </h2>
                                        <p className="text-muted-foreground text-sm">
                                            {product.brand || product.category}
                                        </p>
                                    </div>
                                    <StockStatus
                                        status={product.stock_status}
                                    />
                                </div>
                                <div>
                                    <p className="text-3xl font-bold">
                                        {product.stock_quantity}
                                    </p>
                                    <p className="text-muted-foreground text-sm">
                                        {product.stock_display} · minimum{' '}
                                        {product.minimum_stock}
                                    </p>
                                </div>
                                <div className="grid grid-cols-2 gap-2">
                                    {permissions?.recordEntries && (
                                        <Button asChild>
                                            <Link
                                                href={createEntry({
                                                    query: {
                                                        product: product.id,
                                                    },
                                                })}
                                            >
                                                <ArrowDownToLine /> Entrée
                                            </Link>
                                        </Button>
                                    )}
                                    <Button asChild variant="outline">
                                        <Link
                                            href={createExit({
                                                query: { product: product.id },
                                            })}
                                        >
                                            <ArrowUpFromLine /> Sortie
                                        </Link>
                                    </Button>
                                </div>
                            </CardContent>
                        </Card>
                    ))}
                </div>
                {products.length === 0 && (
                    <p className="text-muted-foreground rounded-lg border py-12 text-center">
                        Aucun produit trouvé.
                    </p>
                )}
            </div>
        </>
    );
}

StockIndex.layout = { breadcrumbs: [{ title: 'Stock', href: index() }] };
