import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';
import { User } from './user';
import {Md5} from 'ts-md5/dist/md5';

@Injectable({
    providedIn: 'root'
})

export class UserService {

    private userName: string;
    private userSubject = new BehaviorSubject<User>(null);
    private userDataKey: string = Md5.hashStr('userDataSai');

    constructor() {
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
        this.userSubject.next(user);
    }

    logout() {
        // this.tokenService.removeToken();
        // this.userSubject.next(null);

        window.localStorage.removeItem(this.userDataKey);
        this.userSubject.next(null);
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
}
