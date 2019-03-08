import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { HttpClientModule } from '@angular/common/http';
import { AppComponent } from './app.component';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { AppRoutingModule } from './app.routing.module';

import { HomeModule } from './home/home.module';
import { MenuModule } from './menu/menu.module';
import { CoreModule } from './core/core.module';
import { DashboardModule } from './dashboard/dashboard.module';
import { RegisterModule } from './register/register.module';
import { DateAdapter } from '@angular/material';
import { CustomDateAdapter } from './shared/CustomDateAdapter';
import { ReportModule } from './report/report.module';
import { StudentModule } from './student/student.module';
import { ClassesModule } from './classes/classes.module';
import { PresenceModule } from './presence/presence.module';
import { MaterialModule } from './material/material.module';



@NgModule({
  declarations: [
    AppComponent
  ],
  imports: [
    BrowserModule,
    BrowserAnimationsModule,
    HttpClientModule,
    AppRoutingModule,
    HomeModule,
    MenuModule,
    CoreModule,
    DashboardModule,
    RegisterModule,
    ReportModule,
    StudentModule,
    ClassesModule,
    PresenceModule,
    MaterialModule
  ],
  providers: [
    {provide: DateAdapter, useClass: CustomDateAdapter}
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
