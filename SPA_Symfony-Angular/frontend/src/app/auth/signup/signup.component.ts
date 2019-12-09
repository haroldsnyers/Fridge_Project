import { Component, AfterViewInit, OnInit } from '@angular/core';
import { NgForm, FormGroup, FormControl, Validators, FormBuilder } from '@angular/forms';
import { AuthService } from '../auth.service';
import { Router } from '@angular/router';
import { CustomValidators, errorMessages, ConfirmValidParentMatcher } from 'src/app/validators/CustomValidators.module';

@Component({
  // selector: 'app-signup',
  templateUrl: './signup.component.html',
  styleUrls: ['./signup.component.css']
})
export class SignupComponent implements OnInit, AfterViewInit {
  isLoading = false;
  hide = true;
  form: FormGroup;
  Error: string;

  confirmValidParentMatcher = new ConfirmValidParentMatcher();

  errors = errorMessages;
  private history = false;

  constructor(
    public authService: AuthService,
    private router: Router,
    private formBuilder: FormBuilder) { }

  ngOnInit() {
    this.form = this.formBuilder.group({
      username: ['', [
          Validators.required,
          Validators.minLength(1),
          Validators.maxLength(128)
      ]],
      email: ['', [
          Validators.required,
          Validators.email
      ]],
      passwordGroup: this.formBuilder.group({
          password: ['', [
              Validators.required
          ]],
          passwordConfirmation: ['', Validators.required]
      }, { validator: CustomValidators.childrenEqual})
    });
    if (this.history) {
      this.form.setValue({
        // tslint:disable:object-literal-key-quotes
        'email': this.authService.getHistory()[0],
        'username': this.authService.getHistory()[1],
        'password': this.authService.getHistory()[2],
        'passwordConfirmation': this.authService.getHistory()[2]
      });
    }
  }


  ngAfterViewInit(): void {
    this.authService.getAuthStatusListener()
    .subscribe(
      auth => {
        if (auth) {
          this.router.navigate(['/fridges']);
        }
        this.authService.getErrorListener().subscribe(
          next => {
            this.Error = next;
          },
          error => {
            this.Error = error;
            this.isLoading = false;
            this.history = true;
          }
        );
      }
    );
  }

  onSignup() {
    if (this.form.invalid) {
      return;
    }
    this.isLoading = true;
    this.authService.createUser(
      this.form.value.email,
      this.form.value.username,
      this.form.value.passwordGroup.password,
      this.form.value.passwordGroup.passwordConfirmation);
    this.authService.setHistorySignup(this.form.value.email, this.form.value.username, this.form.value.passwordGroup.password);
    this.ngAfterViewInit();
  }

}
