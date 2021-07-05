import { Component, OnInit } from '@angular/core';
import { NavController, NavParams, MenuController } from '@ionic/angular';
import Quagga from 'quagga';
declare var Quagga: any;
import { Router } from '@angular/router';

@Component({
  selector: 'app-scanner',
  templateUrl: './scanner.page.html',
  styleUrls: ['./scanner.page.scss'], providers: [NavParams]

})
export class ScannerPage implements OnInit {

  constructor(public navCtrl: NavController,
    public navParams: NavParams,
    public menu: MenuController, private router: Router) {
  }
  ngOnInit() {
    //WARNING:
    //Error: Types of property 'lift' are incompatible -> means
    //that the used typescript version is too high. Works with: 2.3.4 atm

    Quagga.init({
      inputStream: {
        name: "Live",
        type: "LiveStream",
        constraints: {
          width: window.innerWidth,
          height: window.innerHeight,
          facingMode: "environment"
        },
        area: {
          top: "0%",
          right: "0%",
          left: "0%",
          bottom: "0%"
        },
        // Or '#yourElement' (optional)
        target: document.querySelector('#scanner')
      },
      locator: {
        patchSize: "medium",
        halfSample: true
      },
      numOfWorkers: (navigator.hardwareConcurrency ? navigator.hardwareConcurrency : 4),
      decoder: {
        //Change Reader for the right Codes
        readers: ["code_128_reader",
          "ean_reader",
          "ean_8_reader",
          "code_39_reader",
          "code_39_vin_reader",
          "codabar_reader",
          "upc_reader",
          "upc_e_reader",
          "i2of5_reader"],
      },
      locate: true
    }, function (err) {
      if (err) {
        console.log(err);
        return
      }
      console.log("Initialization finished. Ready to start");
      Quagga.start();
    });
    // Make sure, QuaggaJS draws frames an lines around possible
    // barcodes on the live stream
    // Quagga.onProcessed(function (result) {
    //   var drawingCtx = Quagga.canvas.ctx.overlay,
    //     drawingCanvas = Quagga.canvas.dom.overlay;

    //   // if (result) {
    //   //   if (result.boxes) {
    //   //     drawingCtx.clearRect(0, 0, parseInt(drawingCanvas.getAttribute("width")), parseInt(drawingCanvas.getAttribute("height")));
    //   //     result.boxes.filter(function (box) {
    //   //       return box !== result.box;
    //   //     }).forEach(function (box) {
    //   //       Quagga.ImageDebug.drawPath(box, { x: 0, y: 1 }, drawingCtx, { color: "green", lineWidth: 2 });
    //   //     });
    //   //   }

    //   //   if (result.box) {
    //   //     Quagga.ImageDebug.drawPath(result.box, { x: 0, y: 1 }, drawingCtx, { color: "#00F", lineWidth: 2 });
    //   //   }

    //   //   if (result.codeResult && result.codeResult.code) {
    //   //     Quagga.ImageDebug.drawPath(result.line, { x: 'x', y: 'y' }, drawingCtx, { color: 'red', lineWidth: 3 });
    //   //   }
    //   // }
    // });
    // Once a barcode had been read successfully, stop quagga and
    // close the modal after a second to let the user notice where
    // the barcode had actually been found.

    var comp = this;
    Quagga.onDetected(function (result) {
      if (result.codeResult.code) {
        // Was passieren soll wenn ein Code gescannt wurde
        //  $('#scanner_input').val(result.codeResult.code);
        comp.router.navigateByUrl('shipment?id=' + result.codeResult.code);

      }
    });
  }
  ionViewWillLeave() {
    Quagga.stop();
  }

}
