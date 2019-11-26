import { Component, OnInit, OnDestroy } from '@angular/core';
import { Subscription } from 'rxjs';
import { FridgeService } from '../fridge.service';
import { ActivatedRoute, ParamMap } from '@angular/router';
import { FormGroup, FormControl, Validators } from '@angular/forms';
import { Fridge } from '../fridge.model';

@Component({
  selector: 'app-fridge-list',
  templateUrl: './fridge-create.component.html',
  styleUrls: ['./fridge-create.component.css']
})
export class FridgeCreateComponent implements OnInit {
  enteredTitle = '';
  enteredContent = '';
  fridge: Fridge;
  isLoading = false;
  form: FormGroup; // needs to be initialized after (for example in ngOnInit)
  // imagePreview: string;
  mode = 'create';
  protected fridgeId: number;
  types = [
    'french door fridge',
    'side by side fridge',
    'freezerless fridge',
    'bottom freezer fridge',
    'top freezer fridge',
    'freezer',
    'wine fridge'
  ];

  constructor(public fridgeService: FridgeService, public route: ActivatedRoute) {}

  ngOnInit() {
    // configure form
    this.form = new FormGroup({
      // assing key-values pairs to subscribe to controls
      name: new FormControl(null, {
        validators: [Validators.required, Validators.minLength(3)]
      }),
      type: new FormControl(null, {validators : [Validators.required]}),
      nbrFloors: new FormControl(null, {validators : [Validators.required]})
    });
    // listen to the changes in the url, more specifically the parameters
    this.route.paramMap.subscribe((paramMap: ParamMap) => {
      if (paramMap.has('fridgeId')) {
        this.mode = 'edit';
        this.fridgeId = +paramMap.get('fridgeId');
        this.isLoading = true;
        this.fridge = this.fridgeService.getFridge(this.fridgeId);
        console.log(this.fridge);
        this.isLoading = false;
        // to overwrite the initial values of the form
        this.form.setValue({
          // tslint:disable:object-literal-key-quotes
          'name': this.fridge.name,
          'type': this.fridge.type,
          'nbrFloors': this.fridge.nbrFloors
        });
      } else {
        this.mode = 'create';
        this.fridgeId = null;
      }
    });
  }

  onSaveFridge() {
    console.log(this.form.value);
    if (this.form.invalid) {
      return;
    }
    this.isLoading = true;
    if (this.mode === 'create') {
      this.fridgeService.addFridge(
        this.form.value.name,
        this.form.value.type,
        this.form.value.nbrFloors
        );
    } else {
      this.fridgeService.updateFridge(
        this.fridge.id,
        this.form.value.name,
        this.form.value.type,
        this.form.value.nbrFloors,
        this.fridge.user_id
        );
      }
    this.form.reset();
  }
}
