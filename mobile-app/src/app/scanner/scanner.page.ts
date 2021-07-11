import { Component, OnInit } from '@angular/core';
import { NavController, NavParams, MenuController } from '@ionic/angular';
// import { AndroidPermissions } from '@ionic-native/android-permissions/ngx';
import { PhotoService } from '../services/photo.service';
import { ApiService } from '../Service/api.service';

import Quagga from 'quagga';
declare var Quagga: any;
import { Router } from '@angular/router';
// https://ourcodeworld.com/articles/read/460/how-to-create-a-live-barcode-scanner-using-the-webcam-in-javascript
@Component({
  selector: 'app-scanner',
  templateUrl: './scanner.page.html',
  styleUrls: ['./scanner.page.scss'], providers: [NavParams]

})
export class ScannerPage implements OnInit {

  constructor(
    public photoService: PhotoService,
    public navCtrl: NavController,
    public navParams: NavParams,
    public api: ApiService,
    public menu: MenuController, private router: Router) {
  }

  sortByFrequency(array) {
    var frequency = {};
    var sortAble = [];
    var newArr = [];

    array.forEach(function (value) {
      if (value in frequency)
        frequency[value] = frequency[value] + 1;
      else
        frequency[value] = 1;
    });


    for (var key in frequency) {
      sortAble.push([key, frequency[key]])
    }

    sortAble.sort(function (a, b) {
      return b[1] - a[1]
    })


    sortAble.forEach(function (obj) {
      for (var i = 0; i < obj[1]; i++) {
        newArr.push(obj[0]);
      }
    })
    return newArr;

  }

  ngOnInit() {

  }

  async addPhotoToGallery() {

    await this.photoService.addNewToGallery();

    //WARNING:
    //Error: Types of property 'lift' are incompatible -> means
    //that the used typescript version is too high. Works with: 2.3.4 atm



    var breakme = false;

    for (let index = 0; index < this.photoService.photos.length; index++) {
      const element = this.photoService.photos[index];

      if (breakme) {
        continue;
      }


      // Quagga.init({
      //   // inputStream: {
      //   //   name: "Live",
      //   //   type: "LiveStream",
      //   //   numOfWorkers: navigator.hardwareConcurrency,
      //   //   target: document.querySelector('#scanner')
      //   // },
      //   src: element.webviewPath,
      //   numOfWorkers: (navigator.hardwareConcurrency ? navigator.hardwareConcurrency : 4),
      //   decoder: {
      //     //Change Reader for the right Codes
      //     readers: ["code_128_reader",
      //       "ean_reader",
      //       "ean_8_reader",
      //       "code_39_reader",
      //       "code_39_vin_reader",
      //       "codabar_reader",
      //       "upc_reader",
      //       "upc_e_reader",
      //       "i2of5_reader"],
      //   },
      //   locate: true
      // }, function (err) {
      //   if (err) {
      //     console.log(err);
      //     return
      //   }
      //   Quagga.initialized = true;
      //   Quagga.start();
      // });

      var comp = this;

      var last_result = [];
      // if (Quagga.initialized == undefined) {
      //   Quagga.onDetected(function (result) {
      //     if (result.codeResult.code) {
      //       var last_code = result.codeResult.code;
      //       last_result.push(last_code);
      //       if (last_result.length > 50) {
      //         var code = comp.sortByFrequency(last_result)[0];
      //         last_result = [];
      //         comp.router.navigateByUrl('shipment?id=' + code);
      //         Quagga.stop();
      //       }
      //     }
      //   });
      // }



      var comp = this;

      Quagga.decodeSingle({
        src: element.webviewPath,
        numOfWorkers: navigator.hardwareConcurrency,  // Needs to be 0 when used within node
        decoder: {
          readers: [
            "code_128_reader",
            "ean_reader",
            "ean_8_reader",
            "code_39_reader",
            "code_39_vin_reader",
            "upc_reader",
            "upc_e_reader",
          ] // List of active readers

        }

      }, function (result) {

        console.log(result);

        if (result) {
          if (result.codeResult) {
            var last_code = result.codeResult.code;
            last_result.push(last_code);
            breakme = true;
            var code = comp.sortByFrequency(last_result)[0];
            last_result = [];
            comp.router.navigateByUrl('shipment?id=' + code);
            comp.photoService.photos = []
            return;

          }

        }

        comp.api.toastMsg('Please try again!');

      });

    }





  }
  ionViewWillLeave() {
    Quagga.stop();
  }
  onBack() {
    this.router.navigateByUrl("/tabs/pending");
  }
}
