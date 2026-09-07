import { Form, Head, Link, usePage } from '@inertiajs/react';
import { ArrowDownToLine, ArrowUpFromLine, Search } from 'lucide-react';
import { StockStatus } from '@/components/stock-status';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { index } from '@/routes/stock';
import { create as createEntry } from '@/routes/stock/entries';
import { create as createExit } from '@/routes/stock/exits';

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
            <div className="flex flex-1 flex-col gap-5 p-4 md:p-6">
                <div className="grid gap-4">
                    <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h1 className="text-2xl font-bold tracking-tight">
                                Stock
                            </h1>
                            <p className="text-muted-foreground text-sm">
                                {products.length} produit(s)
                            </p>
                        </div>
                        <div className="grid grid-cols-2 gap-2 sm:flex">
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
                        </div>
                    </div>

                    <Form
                        {...index.form()}
                        className="grid gap-2 sm:grid-cols-[1fr_180px_auto]"
                    >
                        <div className="relative">
                            <Search className="text-muted-foreground pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2" />
                            <Input
                                name="search"
                                defaultValue={filters.search}
                                placeholder="Rechercher"
                                className="h-12 pl-10"
                            />
                        </div>
                        <select
                            name="level"
                            defaultValue={filters.level ?? ''}
                            className="border-input bg-background h-12 rounded-md border px-3"
                        >
                            <option value="">Tous</option>
                            <option value="low">Faible</option>
                            <option value="out">Rupture</option>
                        </select>
                        <Button
                            type="submit"
                            variant="secondary"
                            className="h-12"
                        >
                            OK
                        </Button>
                    </Form>
                </div>

                <div className="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                    {products.map((product) => (
                        <Card key={product.id} className="py-4">
                            <CardContent className="grid gap-4">
                                <div className="flex items-start justify-between gap-3">
                                    <div className="min-w-0">
                                        <h2 className="text-base font-semibold">
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

                                <div className="flex items-end justify-between gap-3">
                                    <div>
                                        <p className="text-muted-foreground text-xs">
                                            Quantité
                                        </p>
                                        <p className="text-5xl font-bold">
                                            {product.stock_quantity}
                                        </p>
                                    </div>
                                    <p className="text-muted-foreground pb-2 text-sm">
                                        min. {product.minimum_stock}
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
