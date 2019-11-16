import { Component, OnInit, OnDestroy } from '@angular/core';
import { Subscription } from 'rxjs';
import { ActivatedRoute, ParamMap } from '@angular/router';
import { FormGroup, FormControl, Validators } from '@angular/forms';

@Component({
  selector: 'app-floor-create',
  templateUrl: './floor-create.component.html',
  styleUrls: ['./floor-create.component.css']
})
export class FloorCreateComponent implements OnInit {
  enteredTitle = '';
  enteredContent = '';
  // fridge: Fridge;
  isLoading = false;
  form: FormGroup; // needs to be initialized after (for example in ngOnInit)
  imagePreview: string;
  private mode = 'create';
  private postId: string;
  animals = ['Dog', 'Cat'];

  constructor(public floorService, public route: ActivatedRoute) {}

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
  }

  // onSaveFloor() {
  //   console.log(this.form.value);
  //   if (this.form.invalid) {
  //     return;
  //   }
  //   this.isLoading = true;
  //   this.fridgeService.addFridge(
  //     this.form.value.name,
  //     this.form.value.type,
  //     this.form.value.nbrFloors
  //     );
  //   this.form.reset();
  // }
}
