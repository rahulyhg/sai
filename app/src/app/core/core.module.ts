import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HeaderComponent } from './header/header.component';
import { MatToolbarModule } from '@angular/material';
import { MenuModule } from '../menu/menu.module';

@NgModule({
    declarations: [
        HeaderComponent
    ],
    imports: [
        CommonModule,
        MatToolbarModule,
        MenuModule
    ],
    exports: [
        HeaderComponent
    ]
})

export class CoreModule { }
