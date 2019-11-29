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
    private foodUpdated = new Subject<{listOfFood: Food[]}>();

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

        this.http
            .get(
                this.url + '/api/food/', {params: floorData})
            .subscribe(getData => {
                this.data = JSON.parse(JSON.stringify(getData));
                for (let i = 0; i < Object.keys(this.data).length; i++) {
                    const floor: Floor = {
                        id: this.data[i].idFloor.id,
                        name: this.data[i].idFloor.name,
                        type: this.data[i].idFloor.type,
                        qtyfood: this.data[i].idFloor.qtyfood,
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
            });
    }

    getFoodLists(listFloorId) {
        this.foodList = [];
        // tslint:disable-next-line:prefer-for-of
        for (let i = 0; i < listFloorId.length; i++) {
            this.getFoodList(listFloorId[i]);
        }
    }

    getFood(idFood: number) {
        console.log(this.foodList);
        // tslint:disable-next-line:prefer-for-of
        for (let i = 0; i < this.foodList.length; i++) {
            if (this.foodList[i].id === idFood) {
                return this.foodList[i];
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
        console.log(foodData);
        this.http
            .post(this.url + '/api/food/', foodData)
            .subscribe(response => {
                this.router.navigate(['/fridge/floors']);
            });
    }

    updateFood(idFood: number, name: string, type, idFloor: number, expirationDate: Date,
               quantity: number, dateOfPurchase: Date, imageFoodPath: string, unitQty: string) {
        let foodData: Food | FormData;
        const floor = this.floorService.getFloor(idFloor);
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
        console.log(foodData);
        this.http
          .put(this.url + '/api/food/' + idFood, foodData)
          .subscribe(response => {
                this.router.navigate(['/fridge/floors']);
          });
    }

    deleteFood(idFood: number) {
        return this.http.delete(this.url + '/api/food/' + idFood);
    }
}
