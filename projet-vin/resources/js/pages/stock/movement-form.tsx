import { Form, Head, Link } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import InputError from '@/components/input-error';
import { PageHeader } from '@/components/page-header';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { index as stockIndex } from '@/routes/stock';
import { store as storeEntry } from '@/routes/stock/entries';
import { store as storeExit } from '@/routes/stock/exits';

type MovementProduct = {
    id: number;
    name: string;
    brand: string | null;
    stock_quantity: number;
    bottles_per_case: number;
};
type Reason = { value: string; label: string };

export default function MovementForm({
    direction,
    products,
    reasons,
    selectedProductId,
}: {
    direction: 'entry' | 'exit';
    products: MovementProduct[];
    reasons: Reason[];
    selectedProductId: number | null;
}) {
    const [productId, setProductId] = useState(String(selectedProductId ?? ''));
    const [quantity, setQuantity] = useState(1);
    const [unit, setUnit] = useState<'bottle' | 'case'>('bottle');
    const product = products.find((item) => item.id === Number(productId));
    const bottleQuantity = useMemo(
        () =>
            quantity * (unit === 'case' ? (product?.bottles_per_case ?? 1) : 1),
        [quantity, unit, product],
    );
    const resultingStock = product
        ? product.stock_quantity +
          (direction === 'entry' ? bottleQuantity : -bottleQuantity)
        : 0;
    const isExit = direction === 'exit';

    return (
        <>
            <Head title={isExit ? 'Sortie de stock' : 'Entrée de stock'} />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <PageHeader
                    title={isExit ? 'Sortie de stock' : 'Entrée de stock'}
                    description="Le stock est toujours enregistré en bouteilles."
                />
                <div className="grid max-w-5xl gap-6 lg:grid-cols-[1fr_320px]">
                    <Card>
                        <CardContent>
                            <Form
                                {...(isExit
                                    ? storeExit.form()
                                    : storeEntry.form())}
                                className="grid gap-5"
                            >
                                {({ errors, processing }) => (
                                    <>
                                        <Field
                                            label="Produit *"
                                            error={errors.product_id}
                                        >
                                            <select
                                                name="product_id"
                                                required
                                                value={productId}
                                                onChange={(event) =>
                                                    setProductId(
                                                        event.target.value,
                                                    )
                                                }
                                                className="border-input bg-background h-12 w-full rounded-md border px-3"
                                            >
                                                <option value="" disabled>
                                                    Sélectionner un produit
                                                </option>
                                                {products.map((item) => (
                                                    <option
                                                        key={item.id}
                                                        value={item.id}
                                                    >
                                                        {item.name}
                                                        {item.brand
                                                            ? ` — ${item.brand}`
                                                            : ''}{' '}
                                                        (stock :{' '}
                                                        {item.stock_quantity})
                                                    </option>
                                                ))}
                                            </select>
                                        </Field>
                                        <div className="grid gap-5 sm:grid-cols-2">
                                            <Field
                                                label="Quantité *"
                                                error={errors.quantity}
                                            >
                                                <Input
                                                    name="quantity"
                                                    type="number"
                                                    min="1"
                                                    value={quantity}
                                                    onChange={(event) =>
                                                        setQuantity(
                                                            Math.max(
                                                                1,
                                                                Number(
                                                                    event.target
                                                                        .value,
                                                                ),
                                                            ),
                                                        )
                                                    }
                                                    required
                                                    className="h-12 text-lg"
                                                />
                                            </Field>
                                            <Field
                                                label="Unité *"
                                                error={errors.unit}
                                            >
                                                <select
                                                    name="unit"
                                                    value={unit}
                                                    onChange={(event) =>
                                                        setUnit(
                                                            event.target
                                                                .value as
                                                                | 'bottle'
                                                                | 'case',
                                                        )
                                                    }
                                                    className="border-input bg-background h-12 w-full rounded-md border px-3"
                                                >
                                                    <option value="bottle">
                                                        Bouteille
                                                    </option>
                                                    <option value="case">
                                                        Carton
                                                    </option>
                                                </select>
                                            </Field>
                                        </div>
                                        {isExit ? (
                                            <Field
                                                label="Motif *"
                                                error={errors.reason}
                                            >
                                                <select
                                                    name="reason"
                                                    required
                                                    defaultValue="sale"
                                                    className="border-input bg-background h-12 w-full rounded-md border px-3"
                                                >
                                                    {reasons.map((reason) => (
                                                        <option
                                                            key={reason.value}
                                                            value={reason.value}
                                                        >
                                                            {reason.label}
                                                        </option>
                                                    ))}
                                                </select>
                                            </Field>
                                        ) : (
                                            <Field
                                                label="Nouveau prix d'achat (facultatif)"
                                                error={errors.purchase_price}
                                            >
                                                <Input
                                                    name="purchase_price"
                                                    type="number"
                                                    min="0"
                                                    step="0.01"
                                                    className="h-12"
                                                />
                                            </Field>
                                        )}
                                        <Field
                                            label="Commentaire"
                                            error={errors.note}
                                        >
                                            <textarea
                                                name="note"
                                                rows={3}
                                                className="border-input bg-background rounded-md border p-3"
                                            />
                                        </Field>
                                        <div className="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                                            <Button
                                                asChild
                                                variant="outline"
                                                size="lg"
                                            >
                                                <Link href={stockIndex()}>
                                                    Annuler
                                                </Link>
                                            </Button>
                                            <Button
                                                type="submit"
                                                size="lg"
                                                disabled={
                                                    processing ||
                                                    !product ||
                                                    resultingStock < 0
                                                }
                                            >
                                                {processing && <Spinner />}
                                                {isExit
                                                    ? 'Valider la sortie'
                                                    : "Valider l'entrée"}
                                            </Button>
                                        </div>
                                    </>
                                )}
                            </Form>
                        </CardContent>
                    </Card>
                    <Card className="h-fit">
                        <CardHeader>
                            <CardTitle>Prévisualisation</CardTitle>
                        </CardHeader>
                        <CardContent className="grid gap-4">
                            <Preview
                                label="Stock actuel"
                                value={product?.stock_quantity ?? 0}
                            />
                            <Preview
                                label={
                                    isExit
                                        ? 'Quantité retirée'
                                        : 'Quantité ajoutée'
                                }
                                value={bottleQuantity}
                            />
                            <div className="border-t pt-4">
                                <Preview
                                    label={
                                        isExit
                                            ? 'Stock restant'
                                            : 'Nouveau stock'
                                    }
                                    value={resultingStock}
                                    strong
                                    danger={resultingStock < 0}
                                />
                            </div>
                            {unit === 'case' && product && (
                                <p className="text-muted-foreground text-sm">
                                    {quantity} carton(s) ×{' '}
                                    {product.bottles_per_case} ={' '}
                                    {bottleQuantity} bouteilles
                                </p>
                            )}
                        </CardContent>
                    </Card>
                </div>
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
function Preview({
    label,
    value,
    strong,
    danger,
}: {
    label: string;
    value: number;
    strong?: boolean;
    danger?: boolean;
}) {
    return (
        <div className="flex items-center justify-between gap-4">
            <span className="text-muted-foreground">{label}</span>
            <span
                className={`${strong ? 'text-2xl font-bold' : 'font-semibold'} ${danger ? 'text-red-700' : ''}`}
            >
                {value} bouteilles
            </span>
        </div>
    );
}

MovementForm.layout = { breadcrumbs: [{ title: 'Stock', href: stockIndex() }] };
