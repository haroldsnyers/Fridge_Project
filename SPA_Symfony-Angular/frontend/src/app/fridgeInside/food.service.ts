import { Injectable } from '@angular/core';
import { Subject, Observable } from 'rxjs';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';

import { AuthService } from '../auth/auth.service';
import { FridgeService } from '../fridge/fridge.service';

import { Food, FoodCreate } from './food.model';
import { Floor } from './floor.model';
import { FloorService } from './floor.service';
import { map } from 'rxjs/operators';
import { ApiService } from '../service/api.service';

const urlHost = 'http://127.0.0.1:8000';
// private url = 'http://localhost:3006';

const typeMap = new Map();
const urlImages = urlHost + '/images/foodDefault/';
typeMap.set('Fish', urlImages + 'fish.png');
typeMap.set('Vegetable', urlImages + 'vegetables.png');
typeMap.set('Cheese', urlImages + 'cheese.png');
typeMap.set('Meat', urlImages + 'meat.png');
typeMap.set('Poultry (chickens, turkeys, geese and ducks,...', urlImages + 'chicken.png');
typeMap.set('Dairy food', urlImages + 'dairy_food.png');
typeMap.set('Condiments (sauce)', urlImages + 'condiments.png');
typeMap.set('Grain food (bread)', urlImages + 'grain_food.png');
typeMap.set('Other', urlImages + 'food.png');

@Injectable({
    providedIn: 'root'
})
export class FoodService {
    private foodList: Food[] = [];
    private data: JSON;
    private foodUpdated = new Subject<{listOfFood: Food[]}>();
    private errorListener: Subject<string> =  new Subject<string>();

    private url = 'food';

    constructor(
        private http: HttpClient,
        private router: Router,
        private authService: AuthService,
        private floorService: FloorService,
        private api: ApiService) {}

    getFoodUpdateListener() {
        return this.foodUpdated.asObservable();
    }

    getErrorListener(): Observable<any> {
        return this.errorListener.asObservable();
    }

    getFoodList(floorId: number) {
        const idFloor = floorId.toString();
        const email = this.authService.getCurrentUser();

        // tslint:disable-next-line:object-literal-shorthand
        const floorData = {email: email, idFloor: idFloor};
        this.foodList = [];
        this.api.getItems(floorData, this.url)
            .subscribe(getData => {
                this.data = JSON.parse(JSON.stringify(getData));
                for (let i = 0; i < Object.keys(this.data).length; i++) {
                    const floor: Floor = {
                        id: this.data[i].idFloor.id,
                        name: this.data[i].idFloor.name,
                        type: this.data[i].idFloor.type,
                        qtyFood: this.data[i].idFloor.qtyFood,
                        id_fridge: this.data[i].idFloor.id_fridge,
                    };
                    const food: Food = {
                        id: this.data[i].id,
                        name: this.data[i].name,
                        type: this.data[i].type,
                        expiration_date: this.data[i].expirationDate,
                        quantity: this.data[i].quantity,
                        id_floor: floor,
                        date_of_purchase: this.data[i].dateOfPurchase,
                        image_food_path: this.data[i].imageFoodPath,
                        unit_qty: this.data[i].unitQty

                    };
                    this.foodList.push(food);
                }
                this.foodUpdated.next({
                    listOfFood: [...this.foodList],
                });
            }, error => {
                this.errorListener.error(error);
                this.errorListener = new Subject<string>();
            });
    }

    // getFoodLists(listFloorId) {
    //     this.foodList = [];
    //     // tslint:disable-next-line:prefer-for-of
    //     for (let i = 0; i < listFloorId.length; i++) {
    //         this.getFoodList(listFloorId[i]);
    //     }
    // }

    getFood(idFood: number) {
        // tslint:disable-next-line:prefer-for-of
        for (let i = 0; i < this.foodList.length; i++) {
            if (this.foodList[i].id === idFood) {
                return this.foodList[i];
            }
        }
    }

    addFood(name: string, type, idFloor: number, expirationDate: Date,
            quantity: number, dateOfPurchase: Date, unitQty: string) {
        const imageFoodPath = typeMap.get(type);

        const foodData: FoodCreate = {
            name,
            type,
            expiration_date: expirationDate,
            quantity,
            date_of_purchase: dateOfPurchase,
            image_food_path: imageFoodPath,
            unit_qty: unitQty,
            id_floor: idFloor};
        this.api.addItem(foodData, this.url)
            .subscribe(response => {
                this.router.navigate(['/fridge/floors']);
            }, error => {
                this.errorListener.error(error);
                this.errorListener = new Subject<string>();
            });
    }

    updateFood(idFood: number, name: string, type, idFloor: number, expirationDate: Date,
               quantity: number, dateOfPurchase: Date, unitQty: string) {
        let foodData: Food | FormData;
        const floor = this.floorService.getFloor(idFloor);

        const imageFoodPath = typeMap.get(type);

        foodData = {
            id : idFood,
            // tslint:disable:object-literal-shorthand
            name: name,
            type: type,
            id_floor: floor,
            expiration_date: expirationDate,
            quantity,
            date_of_purchase: dateOfPurchase,
            image_food_path: imageFoodPath,
            unit_qty: unitQty,
        };
        this.api.updateItem(foodData, idFood, this.url)
            .subscribe(response => {
                this.router.navigate(['/fridge/floors']);
            }, error => {
                this.errorListener.error(error);
                this.errorListener = new Subject<string>();
            });
    }

    deleteFood(idFood: number) {
        return this.api.deleteItem(idFood, this.url);
    }
}
