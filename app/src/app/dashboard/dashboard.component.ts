import { Component, OnInit } from '@angular/core';
import { UserService } from '../user/user.service';
import { AlertService } from '../shared/services/alert/alert.service';

@Component({
    templateUrl: './dashboard.component.html',
})

export class DashboardComponent implements OnInit {

    constructor(
        private userService: UserService,
        private alertService: AlertService) {}

    ngOnInit(): void {

    }

    isStudent(): boolean {

        // tslint:disable-next-line:triple-equals
        return (this.userService.getProfileId() == 1);
    }

}
