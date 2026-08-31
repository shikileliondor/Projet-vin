import { Form, Head, Link } from '@inertiajs/react';
import InputError from '@/components/input-error';
import { PageHeader } from '@/components/page-header';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { index, store, update } from '@/routes/products';
import type { Category, Product } from '@/types';

export default function ProductForm({
    categories,
    product,
}: {
    categories: Category[];
    product: Product | null;
}) {
    const isEditing = product !== null;

    return (
        <>
            <Head
                title={isEditing ? 'Modifier le produit' : 'Ajouter un produit'}
            />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <PageHeader
                    title={
                        isEditing ? 'Modifier le produit' : 'Ajouter un produit'
                    }
                    description="Les champs marqués * sont obligatoires."
                />
                <Card className="max-w-4xl">
                    <CardContent>
                        <Form
                            {...(isEditing
                                ? update.form(product.id)
                                : store.form())}
                            className="grid gap-5"
                            resetOnSuccess={!isEditing}
                        >
                            {({ errors, processing }) => (
                                <>
                                    <div className="grid gap-5 md:grid-cols-2">
                                        <Field
                                            label="Nom *"
                                            error={errors.name}
                                        >
                                            <Input
                                                name="name"
                                                defaultValue={product?.name}
                                                required
                                                className="h-11"
                                            />
                                        </Field>
                                        <Field
                                            label="Catégorie *"
                                            error={errors.category_id}
                                        >
                                            <select
                                                name="category_id"
                                                defaultValue={
                                                    product?.category_id ?? ''
                                                }
                                                required
                                                className="border-input bg-background h-11 w-full rounded-md border px-3"
                                            >
                                                <option value="" disabled>
                                                    Sélectionner
                                                </option>
                                                {categories.map((category) => (
                                                    <option
                                                        key={category.id}
                                                        value={category.id}
                                                    >
                                                        {category.name}
                                                    </option>
                                                ))}
                                            </select>
                                        </Field>
                                        <Field
                                            label="Marque"
                                            error={errors.brand}
                                        >
                                            <Input
                                                name="brand"
                                                defaultValue={
                                                    product?.brand ?? ''
                                                }
                                                className="h-11"
                                            />
                                        </Field>
                                        <Field
                                            label="Millésime"
                                            error={errors.vintage}
                                        >
                                            <Input
                                                name="vintage"
                                                type="number"
                                                defaultValue={
                                                    product?.vintage ?? ''
                                                }
                                                className="h-11"
                                            />
                                        </Field>
                                        <Field
                                            label="Contenance *"
                                            error={errors.volume}
                                        >
                                            <Input
                                                name="volume"
                                                defaultValue={
                                                    product?.volume ?? '75 cl'
                                                }
                                                required
                                                className="h-11"
                                            />
                                        </Field>
                                        <Field
                                            label="Bouteilles par carton *"
                                            error={errors.bottles_per_case}
                                        >
                                            <Input
                                                name="bottles_per_case"
                                                type="number"
                                                min="1"
                                                defaultValue={
                                                    product?.bottles_per_case ??
                                                    6
                                                }
                                                required
                                                className="h-11"
                                            />
                                        </Field>
                                        <Field
                                            label="Prix d'achat"
                                            error={errors.purchase_price}
                                        >
                                            <Input
                                                name="purchase_price"
                                                type="number"
                                                min="0"
                                                step="0.01"
                                                defaultValue={
                                                    product?.purchase_price ??
                                                    ''
                                                }
                                                className="h-11"
                                            />
                                        </Field>
                                        <Field
                                            label="Prix de vente *"
                                            error={errors.selling_price}
                                        >
                                            <Input
                                                name="selling_price"
                                                type="number"
                                                min="0"
                                                step="0.01"
                                                defaultValue={
                                                    product?.selling_price ?? ''
                                                }
                                                required
                                                className="h-11"
                                            />
                                        </Field>
                                        <Field
                                            label="Stock minimum *"
                                            error={errors.minimum_stock}
                                        >
                                            <Input
                                                name="minimum_stock"
                                                type="number"
                                                min="0"
                                                defaultValue={
                                                    product?.minimum_stock ?? 0
                                                }
                                                required
                                                className="h-11"
                                            />
                                        </Field>
                                        <Field
                                            label="Code-barres"
                                            error={errors.barcode}
                                        >
                                            <Input
                                                name="barcode"
                                                defaultValue={
                                                    product?.barcode ?? ''
                                                }
                                                className="h-11"
                                            />
                                        </Field>
                                    </div>
                                    <Field
                                        label="Photo (JPG, PNG ou WebP, 2 Mo max.)"
                                        error={errors.photo}
                                    >
                                        <Input
                                            name="photo"
                                            type="file"
                                            accept="image/jpeg,image/png,image/webp"
                                            className="h-11"
                                        />
                                    </Field>
                                    <div className="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                                        <Button
                                            asChild
                                            variant="outline"
                                            size="lg"
                                        >
                                            <Link href={index()}>Annuler</Link>
                                        </Button>
                                        <Button
                                            type="submit"
                                            size="lg"
                                            disabled={processing}
                                        >
                                            {processing && <Spinner />}
                                            {isEditing
                                                ? 'Enregistrer'
                                                : 'Ajouter le produit'}
                                        </Button>
                                    </div>
                                </>
                            )}
                        </Form>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

function Field({
    label,
    error,
    children,
}: {
    label: string;
    error?: string;
    children: React.ReactNode;
}) {
    return (
        <div className="grid gap-2">
            <Label>{label}</Label>
            {children}
            <InputError message={error} />
        </div>
    );
}

ProductForm.layout = { breadcrumbs: [{ title: 'Produits', href: index() }] };
