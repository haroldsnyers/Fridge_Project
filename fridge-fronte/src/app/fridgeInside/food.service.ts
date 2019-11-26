import { Injectable } from '@angular/core';
import { Subject } from 'rxjs';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';

import { AuthService } from '../auth/auth.service';
import { FridgeService } from '../fridge/fridge.service';

import { Food, FoodCreate } from './food.model';
import { Floor } from './floor.model';
import { Fridge } from '../fridge/fridge.model';
import { EmailValidator } from '@angular/forms';
import { FloorService } from './floor.service';

@Injectable({
    providedIn: 'root'
})
export class FoodService {
    private listOfFoodList = [];
    private foodList: Food[] = [];
    private data: JSON;
    private foodUpdated = new Subject<{listOfFood: Array<Food[]>}>();
    private floor: Floor;
    private fridge: Fridge;

    // private url = 'http://127.0.0.1:8000';
    private url = 'http://localhost:3006';

    constructor(
        private http: HttpClient,
        private router: Router,
        private authService: AuthService,
        private fridgeService: FridgeService,
        private floorService: FloorService) {}

    getFoodUpdateListener() {
        return this.foodUpdated.asObservable();
    }

    getFoodList(floorId: number) {
        const idFloor = floorId.toString();
        const email = this.authService.getCurrentUser();

        // tslint:disable-next-line:object-literal-shorthand
        const floorData = {email: email, idFloor: idFloor};

        const food: Food[] = [];
        this.http
            .get(
                this.url + '/api/food/', {params: floorData})
            .subscribe(getData => {
                this.data = JSON.parse(JSON.stringify(getData));
                for (let i = 0; i < Object.keys(this.data).length; i++) {
                    food.push(this.data[i]);
                }
            });
        return food;
    }

    getFoodLists() {
        const listFloorId = this.floorService.getListFloorIds();
        // tslint:disable-next-line:prefer-for-of
        for (let i = 0; i < listFloorId.length; i++) {
            this.foodList = [];
            this.foodList = this.getFoodList(listFloorId[i]);
            this.listOfFoodList.push(this.foodList);
            this.foodUpdated.next({
                listOfFood: [...this.listOfFoodList],
            });
        }
    }

    getFood(idFood: number) {
        // tslint:disable-next-line:prefer-for-of
        for (let i = 0; i < this.listOfFoodList.length; i++) {
            // tslint:disable-next-line:prefer-for-of
            for (let j = 0; j < this.listOfFoodList[i].length; j++) {
                if (this.listOfFoodList[i][j].id === idFood) {
                    return this.listOfFoodList[i][j];
                }
            }
        }
    }

    addFood(name: string, type, idFloor: number, expirationDate: Date,
            quantity: number, dateOfPurchase: Date, imageFoodPath: string, unitQty: string) {
        const foodData: FoodCreate = {
            name,
            type,
            expiration_date: expirationDate,
            quantity,
            date_of_purchase: dateOfPurchase,
            image_food_path: imageFoodPath,
            unit_qty: unitQty,
            id_floor: idFloor};

        this.http
            .post(this.url + 'api/food/', foodData)
            .subscribe(response => {
                this.router.navigate(['/fridge/floors']);
            });
    }

    updateFood(idFood: number, name: string, type, idFloor: number, expirationDate: Date,
               quantity: number, dateOfPurchase: Date, imageFoodPath: string, unitQty: string) {
        let foodData: Food | FormData;
        foodData = {
            id : idFood,
            // tslint:disable:object-literal-shorthand
            name: name,
            type: type,
            id_floor: idFloor,
            expiration_date: expirationDate,
            quantity,
            date_of_purchase: dateOfPurchase,
            image_food_path: imageFoodPath,
            unit_qty: unitQty,
        };
        this.http
          .put(this.url + '/api/food/' + idFloor, foodData)
          .subscribe(response => {
                this.router.navigate(['/fridge/floors']);
          });
    }

    deleteFood(idFood: number) {
        return this.http.delete(this.url + '/api/food/' + idFood);
    }
}
