import { Component, AfterViewInit } from '@angular/core';
import { NgForm } from '@angular/forms';
import { AuthService } from '../auth.service';
import { Router } from '@angular/router';

@Component({
  // selector: 'app-login', // --> can be ommited because of the routes
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements AfterViewInit {
  hide = true;
  isLoading = false;
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
        this.isLoading = false;
      }
    );
  }

  onLogin(form: NgForm) {
    if (form.invalid) {
      return;
    }
    this.isLoading = true;
    this.authService.loginUser(form.value.email, form.value.password);
    this.ngAfterViewInit();
  }

}
