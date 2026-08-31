import { Form, Head, Link, usePage } from '@inertiajs/react';
import { ChevronDown, Edit3, FolderPlus, ListFilter, Plus } from 'lucide-react';
import { useState } from 'react';
import InputError from '@/components/input-error';
import { PageHeader } from '@/components/page-header';
import { Pagination } from '@/components/pagination';
import { StockStatus } from '@/components/stock-status';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { store as storeCategory } from '@/routes/categories';
import { create, edit, index } from '@/routes/products';
import { update as updateStatus } from '@/routes/products/status';
import type { Category, Paginated, Product } from '@/types';

export default function ProductsIndex({
    products,
    categories,
    filters,
    canManage,
}: {
    products: Paginated<Product>;
    categories: Category[];
    filters: { search?: string; category?: string; status?: string };
    canManage: boolean;
}) {
    const { currency } = usePage().props;
    const [filtersOpen, setFiltersOpen] = useState(
        Boolean(filters.search || filters.category || filters.status),
    );
    const [categoryOpen, setCategoryOpen] = useState(false);

    return (
        <>
            <Head title="Produits" />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <PageHeader
                    title="Produits"
                    description={`${products.total} produit(s)`}
                    actions={
                        canManage ? (
                            <Button asChild size="lg">
                                <Link href={create()}>
                                    <Plus /> Ajouter un produit
                                </Link>
                            </Button>
                        ) : undefined
                    }
                />

                <Card className="py-0 md:py-4">
                    <CardContent className="p-0 md:px-6">
                        <button
                            type="button"
                            className="flex w-full items-center justify-between gap-3 p-4 text-left font-semibold md:hidden"
                            aria-expanded={filtersOpen}
                            aria-controls="product-filters"
                            onClick={() => setFiltersOpen((open) => !open)}
                        >
                            <span className="flex items-center gap-2">
                                <ListFilter className="text-gold size-5" />
                                Filtres
                            </span>
                            <ChevronDown
                                className={`size-5 transition-transform ${filtersOpen ? 'rotate-180' : ''}`}
                            />
                        </button>
                        <Form
                            {...index.form()}
                            id="product-filters"
                            className={`gap-3 px-4 pb-4 md:grid md:grid-cols-[1fr_220px_180px_auto] md:px-0 md:pb-0 ${filtersOpen ? 'grid' : 'hidden'}`}
                        >
                            <Input
                                name="search"
                                defaultValue={filters.search}
                                placeholder="Nom, marque ou code-barres"
                                className="h-11"
                            />
                            <select
                                name="category"
                                defaultValue={filters.category ?? ''}
                                className="border-input bg-background h-11 rounded-md border px-3"
                            >
                                <option value="">Toutes les catégories</option>
                                {categories.map((category) => (
                                    <option
                                        key={category.id}
                                        value={category.id}
                                    >
                                        {category.name}
                                    </option>
                                ))}
                            </select>
                            <select
                                name="status"
                                defaultValue={filters.status ?? ''}
                                className="border-input bg-background h-11 rounded-md border px-3"
                            >
                                <option value="">Tous les statuts</option>
                                <option value="active">Actifs</option>
                                <option value="inactive">Inactifs</option>
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

                {canManage && (
                    <Card className="py-0 md:py-4">
                        <CardContent className="p-0 md:px-6">
                            <button
                                type="button"
                                className="flex w-full items-center justify-between gap-3 p-4 text-left font-semibold md:hidden"
                                aria-expanded={categoryOpen}
                                aria-controls="new-category-form"
                                onClick={() => setCategoryOpen((open) => !open)}
                            >
                                <span className="flex items-center gap-2">
                                    <FolderPlus className="text-gold size-5" />
                                    Ajouter une catégorie
                                </span>
                                <ChevronDown
                                    className={`size-5 transition-transform ${categoryOpen ? 'rotate-180' : ''}`}
                                />
                            </button>
                            <Form
                                {...storeCategory.form()}
                                id="new-category-form"
                                className={`flex-col gap-2 px-4 pb-4 sm:flex-row sm:items-start md:flex md:px-0 md:pb-0 ${categoryOpen ? 'flex' : 'hidden'}`}
                            >
                                {({ errors, processing }) => (
                                    <>
                                        <div className="flex-1">
                                            <Input
                                                name="name"
                                                placeholder="Nouvelle catégorie"
                                                className="h-11"
                                            />
                                            <InputError message={errors.name} />
                                        </div>
                                        <Button
                                            type="submit"
                                            variant="outline"
                                            className="h-11"
                                            disabled={processing}
                                        >
                                            Ajouter la catégorie
                                        </Button>
                                    </>
                                )}
                            </Form>
                        </CardContent>
                    </Card>
                )}

                <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    {products.data.map((product) => (
                        <Card
                            key={product.id}
                            className={`py-5 ${!product.is_active ? 'opacity-60' : ''}`}
                        >
                            <CardContent className="grid gap-4">
                                <div className="flex items-start justify-between gap-3">
                                    <div>
                                        <h2 className="font-semibold">
                                            {product.name}
                                        </h2>
                                        <p className="text-muted-foreground text-sm">
                                            {product.brand || 'Sans marque'} ·{' '}
                                            {product.volume}
                                        </p>
                                    </div>
                                    {product.is_active ? (
                                        <StockStatus
                                            status={product.stock_status}
                                        />
                                    ) : (
                                        <Badge variant="secondary">
                                            Inactif
                                        </Badge>
                                    )}
                                </div>
                                <div className="bg-muted rounded-lg p-3">
                                    <p className="text-2xl font-bold">
                                        {product.stock_quantity}{' '}
                                        <span className="text-sm font-normal">
                                            bouteilles
                                        </span>
                                    </p>
                                    <p className="text-muted-foreground text-xs">
                                        {product.stock_display}
                                    </p>
                                </div>
                                <div className="flex flex-wrap items-center justify-between gap-2 text-sm">
                                    <span>{product.category?.name}</span>
                                    <span className="font-medium">
                                        {Number(
                                            product.selling_price,
                                        ).toLocaleString('fr-FR')}{' '}
                                        {currency}
                                    </span>
                                </div>
                                {canManage && (
                                    <div className="flex gap-2">
                                        <Button
                                            asChild
                                            variant="outline"
                                            className="flex-1"
                                        >
                                            <Link href={edit(product.id)}>
                                                <Edit3 /> Modifier
                                            </Link>
                                        </Button>
                                        <Form
                                            {...updateStatus.form(product.id)}
                                        >
                                            {({ processing }) => (
                                                <>
                                                    <input
                                                        type="hidden"
                                                        name="is_active"
                                                        value={
                                                            product.is_active
                                                                ? '0'
                                                                : '1'
                                                        }
                                                    />
                                                    <Button
                                                        type="submit"
                                                        variant="ghost"
                                                        disabled={processing}
                                                    >
                                                        {product.is_active
                                                            ? 'Désactiver'
                                                            : 'Activer'}
                                                    </Button>
                                                </>
                                            )}
                                        </Form>
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    ))}
                </div>
                {products.data.length === 0 && (
                    <p className="text-muted-foreground rounded-lg border py-12 text-center">
                        Aucun produit trouvé.
                    </p>
                )}
                <Pagination links={products.links} />
            </div>
        </>
    );
}

ProductsIndex.layout = { breadcrumbs: [{ title: 'Produits', href: index() }] };
