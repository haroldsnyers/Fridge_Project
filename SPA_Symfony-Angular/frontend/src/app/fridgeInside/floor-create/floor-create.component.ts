import { Component, OnInit, OnDestroy, AfterViewInit } from '@angular/core';
import { ActivatedRoute, ParamMap } from '@angular/router';
import { FormGroup, FormControl, Validators } from '@angular/forms';
import { Floor } from '../floor.model';
import { FloorService } from '../floor.service';
import { MatSnackBar } from '@angular/material';

@Component({
  selector: 'app-floor-create',
  templateUrl: './floor-create.component.html',
  styleUrls: ['./floor-create.component.css']
})
export class FloorCreateComponent implements OnInit, AfterViewInit {
  enteredTitle = '';
  enteredContent = '';
  floor: Floor;
  isLoading = false;
  form: FormGroup; // needs to be initialized after (for example in ngOnInit)
  // imagePreview: string;
  title = 'Create';
  private mode = 'create';
  private floorId: number;
  Error: string;
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

  constructor(
    public floorService: FloorService,
    public route: ActivatedRoute,
    private snackBar: MatSnackBar) {}

  ngOnInit() {
    // configure form
    this.form = new FormGroup({
      // assing key-values pairs to subscribe to controls
      name: new FormControl(null, {
        validators: [Validators.required, Validators.minLength(3)]
      }),
      type: new FormControl(null, {validators : [Validators.required]})
    });
    this.route.paramMap.subscribe((paramMap: ParamMap) => {
      if (paramMap.has('floorId')) {
        this.mode = 'edit';
        this.title = 'Edit';
        this.floorId = +paramMap.get('floorId');
        this.isLoading = true;
        this.floor = this.floorService.getFloor(this.floorId);
        console.log(this.floor);
        this.isLoading = false;
        this.form.setValue({
          // tslint:disable:object-literal-key-quotes
          'name': this.floor.name,
          'type': this.floor.type,
        });
      } else {
        this.mode = 'create';
        this.title = 'Create';
        this.floorId = null;
      }
      console.log(this.mode);
    });
  }

  ngAfterViewInit(): void {
    this.floorService.getErrorListener().subscribe(
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

  onSaveFloor() {
    console.log(this.form.value);
    if (this.form.invalid) {
      return;
    }
    this.isLoading = true;
    if (this.mode === 'create') {
      this.floorService.addFloor(
        this.form.value.name,
        this.form.value.type
        );
      this.openSnackBar('Floor succesfully Created!', 'OK');
    } else {
      this.floorService.updateFloor(
        this.floor.id,
        this.form.value.name,
        this.form.value.type,
        this.floor.id_fridge
      );
      this.openSnackBar('Floor succesfully updated!', 'OK');
    }
    this.ngAfterViewInit();
    this.form.reset();
  }

  openSnackBar(message: string, action: string) {
    this.snackBar.open(message, action, {
      duration: 4000,
    });
  }
}
