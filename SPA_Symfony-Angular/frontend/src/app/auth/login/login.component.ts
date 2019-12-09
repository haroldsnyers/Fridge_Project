import { Component, AfterViewInit, OnInit } from '@angular/core';
import { NgForm, FormGroup, FormControl, Validators } from '@angular/forms';
import { AuthService } from '../auth.service';
import { Router } from '@angular/router';

@Component({
  // selector: 'app-login', // --> can be ommited because of the routes
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements AfterViewInit, OnInit {
  hide = true;
  isLoading = false;
  Error: string;

  private history = false;

  form: FormGroup;

  constructor(public authService: AuthService, private router: Router) { }

  ngOnInit(): void {
    this.form = new FormGroup({
      // assing key-values pairs to subscribe to controls
      email: new FormControl(null, {
        validators: [Validators.required]
      }),
      password: new FormControl(null, {validators : [Validators.required]})
    });
    if (this.history) {
      this.form.setValue({
        // tslint:disable:object-literal-key-quotes
        'email': this.authService.getHistory()[0],
        'password': this.authService.getHistory()[1]
      });
    }
  }

  ngAfterViewInit(): void {
    this.authService.getAuthStatusListener()
    .subscribe(
      auth => {
        if (auth) {
          this.router.navigate(['']);
        }
        this.authService.getErrorListener().subscribe(
          next => {
            this.Error = next;
          },
          error => {
            this.Error = error;
            this.history = true;
          }
        );
        this.isLoading = false;
      }
    );
  }

  onLogin() {
    if (this.form.invalid) {
      return;
    }
    this.isLoading = true;
    this.authService.loginUser(this.form.value.email, this.form.value.password);
    this.authService.setHistory(this.form.value.email, this.form.value.password);
    this.ngAfterViewInit();
  }

}
