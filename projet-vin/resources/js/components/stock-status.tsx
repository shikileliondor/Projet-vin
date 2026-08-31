import { Badge } from '@/components/ui/badge';

export function StockStatus({ status }: { status: 'normal' | 'low' | 'out' }) {
    const styles = {
        normal: 'border-emerald-200 bg-emerald-100 text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-300',
        low: 'border-orange-200 bg-orange-100 text-orange-800 dark:border-orange-900 dark:bg-orange-950 dark:text-orange-300',
        out: 'border-red-200 bg-red-100 text-red-800 dark:border-red-900 dark:bg-red-950 dark:text-red-300',
    };
    const labels = { normal: 'Normal', low: 'Stock faible', out: 'Rupture' };

    return <Badge className={styles[status]}>{labels[status]}</Badge>;
}
