import { Component, OnInit, OnDestroy, AfterViewInit } from '@angular/core';
import { Subscription } from 'rxjs';
import { ActivatedRoute, ParamMap } from '@angular/router';
import { FormGroup, FormControl, Validators } from '@angular/forms';
import { Food } from '../food.model';
import { FoodService } from '../food.service';
import { FloorService } from '../floor.service';
import { MatSnackBar } from '@angular/material';


@Component({
  selector: 'app-food-create',
  templateUrl: './food-create.component.html',
  styleUrls: ['./food-create.component.css']
})
export class FoodCreateComponent implements OnInit, AfterViewInit {
  food: Food;
  isLoading = false;
  form: FormGroup; // needs to be initialized after (for example in ngOnInit)
  private mode = 'create';
  title = 'Create';
  private foodId: number;
  private floorId: number;
  types = [
    'Vegetable',
    'Cheese',
    'Meat',
    'Poultry (chickens, turkeys, geese and ducks,...',
    'Fish',
    'Dairy food',
    'Condiments (sauce)',
    'Grain food (bread)',
    'Other'
  ];
  units = ['kg', 'g', 'cl', 'L', 'unit(s)'];
  Error: string;

  constructor(
    public foodService: FoodService,
    public route: ActivatedRoute,
    private snackBar: MatSnackBar) {}

  ngOnInit() {
    // configure form
    this.form = new FormGroup({
      // assing key-values pairs to subscribe to controls
      name: new FormControl(null, {
        validators: [Validators.required, Validators.minLength(3)]
      }),
      type: new FormControl(null, {validators : [Validators.required]}),
      expirationDate: new FormControl(null, {validators : [Validators.required]}),
      quantity: new FormControl(null, {validators : [Validators.required]}),
      dateOfPurchase: new FormControl(null, {validators : [Validators.required]}),
      unitQty: new FormControl(null, {validators : [Validators.required]}),
    });
    this.route.paramMap.subscribe((paramMap: ParamMap) => {
      if (paramMap.has('foodId')) {
        this.mode = 'edit';
        this.title = 'Edit';
        this.foodId = +paramMap.get('foodId');
        this.isLoading = true;
        this.food = this.foodService.getFood(this.foodId);
        this.isLoading = false;
        this.form.setValue({
          // tslint:disable:object-literal-key-quotes
          'name': this.food.name,
          'type': this.food.type,
          'expirationDate': this.food.expiration_date,
          'quantity': this.food.quantity,
          'dateOfPurchase': this.food.date_of_purchase,
          'unitQty': this.food.unit_qty
        });
      } else if (paramMap.has('floorId')) {
        this.mode = 'create';
        this.title = 'Create';
        this.foodId = null;
        this.floorId = +paramMap.get('floorId');
      }
    });
  }

  ngAfterViewInit(): void {
    this.foodService.getErrorListener().subscribe(
        next => {
          this.Error = next;
          this.isLoading = false;
        },
        error => {
          this.Error = error;
          this.isLoading = false;
        }
    );
  }

  onSaveFood() {
    console.log(this.form.value);
    if (this.form.invalid) {
      return;
    }
    this.isLoading = true;
    if (this.mode === 'create') {
      this.foodService.addFood(
        this.form.value.name,
        this.form.value.type,
        this.floorId,
        this.form.value.expirationDate,
        this.form.value.quantity,
        this.form.value.dateOfPurchase,
        this.form.value.unitQty
        );
      this.openSnackBar('Food successfully Created!', 'OK');
    } else {
    this.foodService.updateFood(
        this.food.id,
        this.form.value.name,
        this.form.value.type,
        this.food.id_floor.id,
        this.form.value.expirationDate,
        this.form.value.quantity,
        this.form.value.dateOfPurchase,
        this.form.value.unitQty
      );
    this.ngAfterViewInit();
    this.openSnackBar('Food successfully Updated!', 'OK');
    }
    this.form.reset();
  }

  openSnackBar(message: string, action: string) {
    this.snackBar.open(message, action, {
      duration: 4000,
    });
  }
}
