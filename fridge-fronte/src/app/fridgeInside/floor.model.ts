export interface Floor {
    id: number;
    name: string;
    type: string;
    qtyFood: number;
    id_fridge: number;
}

export interface FloorUpdate {
    id: number;
    name: string;
    type: string;
    id_fridge: number;
}

export interface FloorCreate {
    name: string;
    type: string;
    id_fridge: number;
}
