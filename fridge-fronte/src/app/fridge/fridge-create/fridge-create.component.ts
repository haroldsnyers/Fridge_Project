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
  imagePreview: string;
  private mode = 'create';
  private postId: string;
  animals = ['Dog', 'Cat'];

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
  }

  onSaveFridge() {
    console.log(this.form.value);
    if (this.form.invalid) {
      return;
    }
    this.isLoading = true;
    this.fridgeService.addFridge(
      this.form.value.name,
      this.form.value.type,
      this.form.value.nbrFloors
      );
    this.form.reset();
  }
}
