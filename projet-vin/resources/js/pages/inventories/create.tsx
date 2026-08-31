import { Head, Link, useForm } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import InputError from '@/components/input-error';
import { PageHeader } from '@/components/page-header';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { index, store } from '@/routes/inventories';

type InventoryProduct = {
    id: number;
    name: string;
    brand: string | null;
    category: string;
    stock_quantity: number;
};

export default function CreateInventory({
    products,
}: {
    products: InventoryProduct[];
}) {
    const [search, setSearch] = useState('');
    const form = useForm({
        note: '',
        items: products.map((product) => ({
            product_id: product.id,
            physical_quantity: product.stock_quantity,
        })),
    });
    const visibleProducts = useMemo(
        () =>
            products.filter((product) =>
                `${product.name} ${product.brand ?? ''}`
                    .toLowerCase()
                    .includes(search.toLowerCase()),
            ),
        [products, search],
    );
    const itemError = form.errors.items;

    const updateQuantity = (productId: number, value: number) => {
        form.setData(
            'items',
            form.data.items.map((item) =>
                item.product_id === productId
                    ? { ...item, physical_quantity: Math.max(0, value) }
                    : item,
            ),
        );
    };

    return (
        <>
            <Head title="Nouvel inventaire" />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <PageHeader
                    title="Nouvel inventaire"
                    description="Saisissez la quantité physique de chaque produit. Les écarts ajusteront automatiquement le stock."
                />
                <form
                    onSubmit={(event) => {
                        event.preventDefault();
                        form.post(store.url());
                    }}
                    className="grid gap-5"
                >
                    <Card className="py-4">
                        <CardContent className="grid gap-3">
                            <Label htmlFor="search">
                                Rechercher dans la liste
                            </Label>
                            <Input
                                id="search"
                                value={search}
                                onChange={(event) =>
                                    setSearch(event.target.value)
                                }
                                placeholder="Nom ou marque"
                                className="h-11"
                            />
                            <InputError message={itemError} />
                        </CardContent>
                    </Card>
                    <div className="grid gap-3">
                        {visibleProducts.map((product) => {
                            const item = form.data.items.find(
                                (entry) => entry.product_id === product.id,
                            )!;
                            const difference =
                                item.physical_quantity - product.stock_quantity;
                            return (
                                <Card key={product.id} className="py-4">
                                    <CardContent className="grid items-center gap-4 sm:grid-cols-[1fr_130px_130px_100px]">
                                        <div>
                                            <p className="font-medium">
                                                {product.name}
                                            </p>
                                            <p className="text-muted-foreground text-sm">
                                                {product.brand ||
                                                    product.category}
                                            </p>
                                        </div>
                                        <div>
                                            <p className="text-muted-foreground text-xs">
                                                Théorique
                                            </p>
                                            <p className="text-lg font-semibold">
                                                {product.stock_quantity}
                                            </p>
                                        </div>
                                        <div>
                                            <Label
                                                htmlFor={`physical-${product.id}`}
                                                className="text-xs"
                                            >
                                                Stock physique
                                            </Label>
                                            <Input
                                                id={`physical-${product.id}`}
                                                type="number"
                                                min="0"
                                                value={item.physical_quantity}
                                                onChange={(event) =>
                                                    updateQuantity(
                                                        product.id,
                                                        Number(
                                                            event.target.value,
                                                        ),
                                                    )
                                                }
                                                className="h-11 text-lg"
                                            />
                                        </div>
                                        <div>
                                            <p className="text-muted-foreground text-xs">
                                                Écart
                                            </p>
                                            <p
                                                className={`text-lg font-bold ${difference < 0 ? 'text-red-700' : difference > 0 ? 'text-emerald-700' : ''}`}
                                            >
                                                {difference > 0 ? '+' : ''}
                                                {difference}
                                            </p>
                                        </div>
                                    </CardContent>
                                </Card>
                            );
                        })}
                    </div>
                    <Card>
                        <CardContent className="grid gap-2">
                            <Label htmlFor="note">Commentaire</Label>
                            <textarea
                                id="note"
                                value={form.data.note}
                                onChange={(event) =>
                                    form.setData('note', event.target.value)
                                }
                                rows={3}
                                className="border-input bg-background rounded-md border p-3"
                            />
                            <InputError message={form.errors.note} />
                        </CardContent>
                    </Card>
                    <div className="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        <Button asChild variant="outline" size="lg">
                            <Link href={index()}>Annuler</Link>
                        </Button>
                        <Button
                            type="submit"
                            size="lg"
                            disabled={form.processing || products.length === 0}
                        >
                            {form.processing && <Spinner />}Valider l'inventaire
                        </Button>
                    </div>
                </form>
            </div>
        </>
    );
}

CreateInventory.layout = {
    breadcrumbs: [{ title: 'Inventaires', href: index() }],
};
