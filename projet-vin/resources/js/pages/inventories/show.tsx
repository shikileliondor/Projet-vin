import { Head, Link } from '@inertiajs/react';
import { PageHeader } from '@/components/page-header';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { index, show } from '@/routes/inventories';

type Item = {
    id: number;
    product: string;
    brand: string | null;
    theoretical_quantity: number;
    physical_quantity: number;
    difference: number;
};
type Inventory = {
    id: number;
    reference: string;
    user: string;
    note: string | null;
    completed_at: string;
    items: Item[];
};

export default function InventoryShow({ inventory }: { inventory: Inventory }) {
    return (
        <>
            <Head title={inventory.reference} />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <PageHeader
                    title={inventory.reference}
                    description={`Validé le ${inventory.completed_at} par ${inventory.user}`}
                    actions={
                        <Button asChild variant="outline">
                            <Link href={index()}>Retour</Link>
                        </Button>
                    }
                />
                {inventory.note && (
                    <Card className="py-4">
                        <CardContent>
                            <p className="text-sm">{inventory.note}</p>
                        </CardContent>
                    </Card>
                )}
                <Card>
                    <CardHeader>
                        <CardTitle>Résultats</CardTitle>
                    </CardHeader>
                    <CardContent className="p-0">
                        <div className="overflow-x-auto">
                            <table className="w-full text-sm">
                                <thead className="bg-muted">
                                    <tr>
                                        <th className="p-3 text-left">
                                            Produit
                                        </th>
                                        <th className="p-3 text-right">
                                            Théorique
                                        </th>
                                        <th className="p-3 text-right">
                                            Physique
                                        </th>
                                        <th className="p-3 text-right">
                                            Écart
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {inventory.items.map((item) => (
                                        <tr key={item.id} className="border-t">
                                            <td className="p-3">
                                                <p className="font-medium">
                                                    {item.product}
                                                </p>
                                                <p className="text-muted-foreground text-xs">
                                                    {item.brand}
                                                </p>
                                            </td>
                                            <td className="p-3 text-right">
                                                {item.theoretical_quantity}
                                            </td>
                                            <td className="p-3 text-right">
                                                {item.physical_quantity}
                                            </td>
                                            <td
                                                className={`p-3 text-right font-bold ${item.difference < 0 ? 'text-red-700' : item.difference > 0 ? 'text-emerald-700' : ''}`}
                                            >
                                                {item.difference > 0 ? '+' : ''}
                                                {item.difference}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

InventoryShow.layout = (props: { inventory: Inventory }) => ({
    breadcrumbs: [
        { title: 'Inventaires', href: index() },
        { title: props.inventory.reference, href: show(props.inventory.id) },
    ],
});
