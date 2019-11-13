export interface Fridge {
    id: string;
    name: string;
    type: string;
    nbrFloors: string;
    user_id: string;
}

export interface FridgeCreate {
    name: string;
    type: string;
    nbrFloors: number;
    userMail: string;
}
