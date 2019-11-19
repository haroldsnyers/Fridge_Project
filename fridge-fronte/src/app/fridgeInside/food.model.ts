export interface Food {
    id: number;
    name: string;
    type: string;
    expiration_date: Date;
    quantity: number;
    id_floor: number;
    date_of_purchase: Date;
    image_food_path: string;
    unit_qty: string;
}