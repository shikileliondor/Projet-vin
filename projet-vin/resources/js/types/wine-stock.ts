export type Category = {
    id: number;
    name: string;
    is_active?: boolean;
};

export type Product = {
    id: number;
    category_id: number;
    category?: Category;
    name: string;
    brand: string | null;
    vintage: number | null;
    volume: string;
    purchase_price: string | null;
    selling_price: string;
    bottles_per_case: number;
    stock_quantity: number;
    minimum_stock: number;
    stock_status: 'normal' | 'low' | 'out';
    stock_display: string;
    barcode: string | null;
    photo_url: string | null;
    is_active: boolean;
};

export type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

export type Paginated<T> = {
    data: T[];
    links: PaginationLink[];
    from: number | null;
    to: number | null;
    total: number;
};
