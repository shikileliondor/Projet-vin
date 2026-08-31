import { Form, Head, Link } from '@inertiajs/react';
import { PageHeader } from '@/components/page-header';
import { Pagination } from '@/components/pagination';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { index as stockIndex } from '@/routes/stock';
import { index } from '@/routes/stock/movements';
import type { Paginated } from '@/types';

type Movement = {
    id: number;
    product: string;
    user: string;
    type: string;
    type_label: string;
    quantity: number;
    reason: string;
    stock_before: number;
    stock_after: number;
    note: string | null;
    created_at: string;
};

export default function Movements({
    movements,
    filters,
    types,
}: {
    movements: Paginated<Movement>;
    filters: { type?: string };
    types: { value: string; label: string }[];
}) {
    return (
        <>
            <Head title="Mouvements" />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <PageHeader
                    title="Historique des mouvements"
                    description={`${movements.total} mouvement(s)`}
                    actions={
                        <Button asChild variant="outline">
                            <Link href={stockIndex()}>Retour au stock</Link>
                        </Button>
                    }
                />
                <Card className="py-4">
                    <CardContent>
                        <Form
                            {...index.form()}
                            className="flex flex-col gap-3 sm:flex-row"
                        >
                            <select
                                name="type"
                                defaultValue={filters.type ?? ''}
                                className="border-input bg-background h-11 flex-1 rounded-md border px-3"
                            >
                                <option value="">Tous les types</option>
                                {types.map((type) => (
                                    <option key={type.value} value={type.value}>
                                        {type.label}
                                    </option>
                                ))}
                            </select>
                            <Button type="submit" variant="outline">
                                Filtrer
                            </Button>
                        </Form>
                    </CardContent>
                </Card>
                <Card className="overflow-hidden py-0">
                    <CardContent className="p-0">
                        <div className="overflow-x-auto">
                            <table className="w-full text-sm">
                                <thead className="bg-muted">
                                    <tr>
                                        <th className="p-3 text-left">Date</th>
                                        <th className="p-3 text-left">
                                            Produit
                                        </th>
                                        <th className="p-3 text-left">Type</th>
                                        <th className="p-3 text-right">
                                            Quantité
                                        </th>
                                        <th className="p-3 text-left">Motif</th>
                                        <th className="p-3 text-left">Stock</th>
                                        <th className="p-3 text-left">
                                            Utilisateur
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {movements.data.map((movement) => (
                                        <tr
                                            key={movement.id}
                                            className="border-t"
                                        >
                                            <td className="p-3 whitespace-nowrap">
                                                {movement.created_at}
                                            </td>
                                            <td className="p-3 font-medium">
                                                {movement.product}
                                            </td>
                                            <td className="p-3">
                                                <Badge variant="outline">
                                                    {movement.type_label}
                                                </Badge>
                                            </td>
                                            <td
                                                className={`p-3 text-right font-bold ${movement.quantity >= 0 ? 'text-emerald-700' : 'text-red-700'}`}
                                            >
                                                {movement.quantity > 0
                                                    ? '+'
                                                    : ''}
                                                {movement.quantity}
                                            </td>
                                            <td className="p-3">
                                                {movement.reason}
                                            </td>
                                            <td className="p-3 whitespace-nowrap">
                                                {movement.stock_before} →{' '}
                                                {movement.stock_after}
                                            </td>
                                            <td className="p-3">
                                                {movement.user}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                        {movements.data.length === 0 && (
                            <p className="text-muted-foreground py-12 text-center">
                                Aucun mouvement.
                            </p>
                        )}
                    </CardContent>
                </Card>
                <Pagination links={movements.links} />
            </div>
        </>
    );
}

Movements.layout = { breadcrumbs: [{ title: 'Mouvements', href: index() }] };
