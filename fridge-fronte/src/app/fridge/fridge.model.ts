export interface Fridge {
    id: number;
    name: string;
    type: string;
    nbrFloors: number;
    user_id: number;
}

export interface FridgeCreate {
    name: string;
    type: string;
    nbrFloors: number;
    userMail: string;
}
