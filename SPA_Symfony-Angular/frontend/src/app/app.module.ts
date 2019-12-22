import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';

import {
  MatInputModule,
  MatCardModule,
  MatButtonModule,
  MatProgressSpinnerModule,
  MatToolbarModule,
  MatExpansionModule,
  MatSelectModule,
  MatSliderModule,
  MatTabsModule,
  MatButtonToggleModule,
  MatTableModule,
  MatPaginatorModule,
  MatChipsModule,
  MatDialogModule,
  MatTooltipModule,
  MatDatepickerModule,
  MatNativeDateModule,
  MatMenuModule,
  MatSnackBarModule,
  MatIconModule,
  MatSnackBar
} from '@angular/material';

import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';


import { HomeComponent } from './home/home.component';
import { HeaderComponent } from './header/header.component';
import { LoginComponent } from './auth/login/login.component';
import { SignupComponent } from './auth/signup/signup.component';
import { FridgeListComponent } from './fridge/fridge-list/fridge-list.component';
import { FridgeCreateComponent } from './fridge/fridge-create/fridge-create.component';
import { FridgeInsideListComponent } from './fridgeInside/fridgeInsideList/fridgeInsideList.component';
import { FloorCreateComponent } from './fridgeInside/floor-create/floor-create.component';
import { DialogDeleteComponent } from './dialog-delete/dialog-delete.component';
import { FoodCreateComponent } from './fridgeInside/food-create/food-create.component';

@NgModule({
  declarations: [
    AppComponent,
    HeaderComponent,
    HomeComponent,
    LoginComponent,
    SignupComponent,
    FridgeListComponent,
    FridgeCreateComponent,
    FridgeInsideListComponent,
    FloorCreateComponent,
    DialogDeleteComponent,
    FoodCreateComponent
  ],
  entryComponents: [DialogDeleteComponent],
  imports: [
    BrowserModule,
    BrowserAnimationsModule,
    FormsModule,
    ReactiveFormsModule,
    AppRoutingModule,
    MatInputModule,
    MatToolbarModule,
    MatCardModule,
    MatButtonModule,
    MatProgressSpinnerModule,
    MatExpansionModule,
    MatSelectModule,
    MatSliderModule,
    MatTabsModule,
    MatButtonToggleModule,
    MatTableModule,
    MatPaginatorModule,
    MatChipsModule,
    MatDialogModule,
    MatTooltipModule,
    MatDatepickerModule,
    MatNativeDateModule,
    MatMenuModule,
    MatIconModule,
    MatSnackBarModule,
    HttpClientModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
