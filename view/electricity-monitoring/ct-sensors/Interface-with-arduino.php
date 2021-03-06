<h2>CT sensors - Interfacing with an Arduino</h2>

<p><img alt="" src="http://lab.megni.co.uk/oemdocs/files/ctarduino.JPG" style="height:223px; width:700px" /></p>

<p>To connect a CT sensor to an Arduino, the output signal from the CT sensor needs to be conditioned so it meets the input requirements of the Arduino analog inputs, i.e. a&nbsp;<strong>positive voltage between 0V and the ADC reference voltage.</strong></p>

<p><strong>Note: </strong>This page give the example of an Arduino board working at 5 V and of the EmonTx working at 3.3 V. Make sure you use the right supply voltage and bias voltage in your calculations that correspond to your setup.</p>

<p>This can be achieved with the following circuit which consists of two main parts:&nbsp;</p>

<ol>
	<li>
	<p>The CT sensor and burden resistor</p>
	</li>
	<li>
	<p>The biasing voltage divider (<em>R1 &amp; R2</em>)</p>
	</li>
</ol>

<p><a href="http://lab.megni.co.uk/oemdocs/files/Arduino AC current input A.png"><img alt="" src="http://lab.megni.co.uk/oemdocs/files/Arduino AC current input A.png" style="height:439px; width:735px" /></a></p>

<p>&nbsp;</p>

<h3><strong><u>Calculating a suitable burden resistor size</u></strong></h3>

<p>If the CT sensor is a &quot;current output&quot; type such as the <em>YHDC&nbsp;SCT-013-000</em>, the current signal needs to be converted to a voltage signal with a burden resistor. If it is a voltage output CT you can skip this step and leave out the burden resistor, as the burden resistor is built into the CT.</p>

<p><strong>1) Choose the current range you want to measure</strong></p>

<p>The YHDC&nbsp;SCT-013-000 CT has a current range of 0 to 100 A. For this example, let&#39;s choose 100 A as our maximum current.</p>

<p><strong>2) Convert maximum RMS current to peak-current by multiplying by &radic;2.</strong></p>

<pre>
Primary peak-current = RMS current &times; &radic;2 = 100 A &times; 1.414 = 141.4A</pre>

<p><strong>3) Divide the peak-current by the number of turns in the CT to give the peak-current in the secondary coil.</strong></p>

<p>The&nbsp;YHDC&nbsp;SCT-013-000 CT has 2000 turns, so the secondary peak current will be:</p>

<pre>
Secondary peak-current = Primary peak-current / no. of turns = 141.4 A / 2000 = 0.0707A</pre>

<p><strong>4) To maximise measurement resolution, the voltage across the burden resistor at peak-current should be equal to one-half of the Arduino analog reference voltage. (AREF / 2)</strong></p>

<p>If you&#39;re using an Arduino running at 5V: AREF / 2 will be 2.5 Volts. So the ideal burden resistance will be:</p>

<pre>
Ideal burden resistance = (AREF/2) / Secondary peak-current = 2.5 V / 0.0707 A = 35.4 &Omega;
</pre>

<p>35 &Omega; is not a common resistor value. The nearest values either side of 35&nbsp;&Omega; are 39 and 33 &Omega;. Always choose the&nbsp;smaller value,&nbsp;or the maximum load current will create a voltage higher than AREF.&nbsp;We recommend a 33 &Omega; &plusmn;1% burden. In some cases, using 2 resistors in series will be closer to the ideal burden value. The further from ideal the value is, the lower the accuracy will be.</p>

<p>Here are the same calculations as above in a more compact form:</p>

<pre>
<strong>Burden Resistor (ohms) = (AREF * CT TURNS) / (2&radic;2 * max primary current)</strong></pre>

<p>&nbsp;</p>

<p><strong>emonTx V2</strong></p>

<p>If you&#39;re using a battery powered emonTx&nbsp;V2, AREF will start at 3.3 V and slowly decrease as the battery voltage drops to 2.7 V. The ideal burden resistance for the minimum voltage would therefore be:</p>

<pre>
Ideal burden resistance = (AREF/2) / Secondary peak-current = 1.35V / 0.0707A = <strong>19.1 &Omega;</strong></pre>

<p>19 &Omega; is not a common value. We have a choice of 18 or 22 &Omega;. We recommend using an 18 &Omega; &plusmn;1% burden.</p>

<p><strong>emonTx V3</strong></p>

<p>The emonTx V3 uses a 3.3V regulator, so it&#39;s V<sub>CC</sub> and therefore&nbsp;AREF, will always be 3.3V regardless of battery voltage. The standard emonTx V3 uses 22 &Omega; burden resistors for CT 1, 2 and 3, and a 120 &Omega; resistor for CT4, the high sensitivity channel. See the emonTx&nbsp;V3 technical wiki at: <a href="https://wiki.openenergymonitor.org/index.php?title=EmonTx_V3#Burden_Resistor_Calculations">https://wiki.openenergymonitor.org/index.php?title=EmonTx_V3#Burden_Resistor_Calculations</a></p>

<p>&nbsp;</p>

<p><strong><a href="https://tyler.anairo.com/?id=5.3.0 ">Tool for calculating burden resistor size, CT turns and max Irms</a>&nbsp;- </strong>thanks to Tyler Adkisson for building and sharing this.</p>

<p>(Note: this tool does not take into account maximum CT power output. Saturation and distortion will occur if the maximum output is exceeded. Nor does it take into account component tolerances, so the burden resistor value should be decreased by a few (~5) percent allow some &quot;headroom.&quot; There is more info about component tolerances at: <a href="https://openenergymonitor.org/emon/buildingblocks/acac-component-tolerances" title="ACAC Component tolerances">ACAC Component tolerances.</a>)</p>

<h3>2) Adding a DC Bias</h3>

<p>If you were to connect one of the CT wires to ground and measure the voltage of the second wire, relative to ground, the voltage would vary from positive to negative with respect to ground. However, the Arduino analog inputs require a <em>positive</em> voltage. By connecting the CT lead we connected to ground, to a source at half the supply voltage instead, the CT output voltage will now swing above and below 2.5 V thus remaining positive.</p>

<p>Resistors R1 &amp; R2 in the circuit diagram above are a voltage divider that provides the 2.5 V source (1.65 V for the emonTx). Capacitor C1 has a low <em>reactance</em> - a few hundred ohms - and provides a path for the alternating current to bypass the resistor. A value of 10 &mu;F is suitable.</p>

<p><strong>Choosing a suitable value for resistors R1 &amp; R2:</strong></p>

<p>Higher resistance lowers quiescent energy consumption.</p>

<p>We use 10 k&Omega; resistors for mains powered monitors. The <a href="https://openenergymonitor.org/emon/emontx">emonTx</a> uses 470 k&Omega; resistors to keep the power consumption to a minimum, as it is intended to run on batteries for several months.</p>

<p>&nbsp;</p>

<h3><strong>Arduino sketch</strong></h3>

<p>To use the above circuit to measure RMS current, with an assumed fixed RMS voltage (e.g. 240V) to indicate approximate apparent power, use this Arduino sketch:&nbsp;<a href="https://openenergymonitor.org/emon/buildingblocks/arduino-sketch-current-only">Arduino sketch - current only</a></p>

