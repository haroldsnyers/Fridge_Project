import { Component, AfterViewInit } from '@angular/core';
import { NgForm } from '@angular/forms';
import { AuthService } from '../auth.service';
import { Router } from '@angular/router';

@Component({
  // selector: 'app-signup',
  templateUrl: './signup.component.html',
  styleUrls: ['./signup.component.css']
})
export class SignupComponent implements AfterViewInit {
  isLoading = false;
  hide = true;
  Error: string;

  constructor(public authService: AuthService, private router: Router) { }

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
          }
        );
      }
    );
    this.isLoading = false;
  }

  onSignup(form: NgForm) {
    if (form.invalid) {
      return;
    }
    this.authService.createUser(form.value.email, form.value.username, form.value.password, form.value.passwordConfirmation);
    this.isLoading = true;
    this.ngAfterViewInit();
  }

}
