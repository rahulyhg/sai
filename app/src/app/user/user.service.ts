import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';
import { User } from './user';
import {Md5} from 'ts-md5/dist/md5';
import { Router } from '@angular/router';
import { environment } from 'src/environments/environment';

@Injectable({
    providedIn: 'root'
})

export class UserService {

    private userName: string;
    private userSubject = new BehaviorSubject<User>(null);
    private userDataKey: string = Md5.hashStr('userDataSai');
    private userUnit: number;
    private userId: number;
    private userProfileId: number;
    private userClass: number;

    constructor(private router: Router) {
        // tslint:disable-next-line:no-unused-expression
        this.isLogged() && this.decodeAndNotify();
    }

    getUser() {
        return this.userSubject.asObservable();
    }


    private decodeAndNotify() {
        const userStringData = this.getUserLoggedData();
        const user = this.decodeData(userStringData);
        this.userName = user.name;
        this.userUnit = user.unit;
        this.userId = user.id;
        this.userProfileId = user.profileId;
        this.userClass = user.class;
        this.userSubject.next(user);
    }

    logout() {

        window.localStorage.removeItem(this.userDataKey);
        this.userSubject.next(null);
        this.router.navigate(['signin']);
    }

    isLogged() {
        return !!this.getUserLoggedData();
    }

    setUserLoggedData(userData) {

        const userDataString = JSON.stringify(userData);
        window.localStorage.setItem(this.userDataKey, btoa(userDataString));
        this.decodeAndNotify();
    }

    getUserLoggedData() {
        return window.localStorage.getItem(this.userDataKey);
    }

    private decodeData(data: string): User {
        return JSON.parse(atob(data));
    }

    getUserName() {
        return this.userName;
    }

    getUserUnit() {
        return this.userUnit;
    }

    getUserId() {
        return this.userId;
    }

    getProfileId(): number {
        return this.userProfileId;
    }

    getUserClass(): number {
        return this.userClass;
    }

    isAdm(): boolean {
        // tslint:disable-next-line:triple-equals
        return (this.userProfileId == 3 || this.userProfileId == 4) ? true : false;
    }
}
