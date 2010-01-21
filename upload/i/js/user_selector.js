var d = document;
var w = window;

var UserSelector	= function()
{
	this.form_input	= false;
	this.container	= false;
	this.avatars_url	= "";
	this.texts	= ({taball:"",tabsel:"",searchinp:""});
	this.data	= [];
	this.onload	= function() { };
	this.seltab	= "all";
	this.selnum	= 0;
};

UserSelector.prototype.init	= function()
{
	if( !this.form_input || !this.container || this.data.length==0 ) {
		this.onload();
		return false;
	}
	var obj	= this;
	var div1	= d.createElement("DIV");
	div1.className	= "filterreguserlist";
	var inp1	= d.createElement("INPUT");
	inp1.id	= "userselector_searchinp";
	inp1.type	= "text";
	inp1.value	= this.texts.searchinp;
	inp1.onkeyup	= function(e) {
		if( !e && w.event ) { e = w.event; }
		if( !e ) { return; }
		var code = e.charCode ? e.charCode : e.keyCode;
		if( code==27 ) {
			this.value	= obj.texts.searchinp;
			obj.search("");
			this.blur();
			return false;
		}
		obj.search(this.value);
	};
	inp1.onfocus	= function() {
		this.value	= obj.trim(this.value);
		if( this.value == obj.texts.searchinp ) {
			this.value	= "";
		}
	};
	inp1.onblur	= function() {
		this.value	= obj.trim(this.value);
		if( this.value == "" ) {
			this.value	= obj.texts.searchinp;
		}
	};
	if( w.postform_forbid_hotkeys_conflicts !== undefined ) {
		postform_forbid_hotkeys_conflicts(inp1);
	}
	div1.appendChild(inp1);
	var lnk1	= d.createElement("A");
	var lnk2	= d.createElement("A");
	lnk1.id	= "userselectortab_all";
	lnk2.id	= "userselectortab_sel";
	lnk1.href	= "javascript:;";
	lnk2.href	= "javascript:;";
	lnk1.onclick	= function() { obj.tab("all"); this.blur(); };
	lnk2.onclick	= function() { obj.tab("sel"); this.blur(); };
	var lnkb1	= d.createElement("B");
	var lnkb2	= d.createElement("B");
	lnkb1.appendChild(d.createTextNode(this.texts.taball));
	lnkb2.appendChild(d.createTextNode(this.texts.tabsel+" ("));
	var lnkb2s	= d.createElement("SPAN");
	lnkb2s.id	= "userselectortab_sel_num";
	lnkb2s.appendChild(d.createTextNode("0"));
	lnkb2.appendChild(lnkb2s);
	lnkb2.appendChild(d.createTextNode(")"));
	lnk1.appendChild(lnkb1);
	lnk2.appendChild(lnkb2);
	div1.appendChild(lnk2);
	div1.appendChild(lnk1);
	this.container.appendChild(div1);
	var div2	= d.createElement("DIV");
	div2.className	= "users";
	var div3	= d.createElement("DIV");
	div3.className	= "theusers";
	div3.id	= "userselector_cnt";
	var i, c, dv, inp, img, dvv, b, s;
	for(i=0; i<this.data.length; i++) {
		c	= this.data[i];
		dv	= d.createElement("DIV");
		dv.id	= "uselector_cnt_"+c[0];
		dv.className	= "selectableuser";
		if( c[5] == 1 ) {
			dv.className	= "selectableuser slctd";
			this.form_input.value	= this.form_input.value + ","+c[0]+",";
			this.selnum	++;
		}
		dv.setAttribute("usel", c[5]==1 ? "1" : "0");
		dv.setAttribute("uindx", i);
		dv.onclick	= function() {
			var i = this.getAttribute("uindx");
			var c = obj.data[i];
			if( c[5] == 0 ) {
				c[5]	= 1;
				obj.form_input.value	= obj.form_input.value + ","+c[0]+",";
				this.className	= "selectableuser slctd";
				d.getElementById("uselector_chk_"+c[0]).checked = true;
				obj.selnum ++;
				d.getElementById("userselectortab_sel_num").innerHTML	= Math.max(obj.selnum, 0);
			}
			else {
				c[5]	= 0;
				obj.form_input.value	= obj.form_input.value.replace(","+c[0]+",", "");
				this.className	= "selectableuser";
				d.getElementById("uselector_chk_"+c[0]).checked = false;
				if( obj.seltab == "sel" ) {
					this.style.visibility	= "hidden";
					var sdf = this;
					setTimeout( function() { sdf.style.display = "none"; sdf.style.visibility = "visible"; }, 200 );
				}
				obj.selnum --;
				d.getElementById("userselectortab_sel_num").innerHTML	= Math.max(obj.selnum, 0);
			}
		};
		dv.style.display	= "none";
		inp	= d.createElement("INPUT");
		inp.id	= "uselector_chk_"+c[0];
		inp.type	= "checkbox";
		inp.checked	= c[5]==1 ? true : false;
		inp.onfocus	= function() { this.blur(); }
		dv.appendChild(inp);
		img	= d.createElement("IMG");
		img.src	= this.avatars_url+c[4];
		dv.appendChild(img);
		dvv	= d.createElement("DIV");
		dvv.className	= "selectableuserside";
		b	= d.createElement("B");
		b.id	= "uselector_unm_"+c[0];
		b.setAttribute("utxt", c[1]);
		b.innerHTML	= c[1];
		dvv.appendChild(b);
		s	= d.createElement("STRONG");
		s.id	= "uselector_fnm_"+c[0];
		s.setAttribute("utxt", c[2]);
		s.innerHTML	= c[2];
		dvv.appendChild(s);
		dv.appendChild(dvv);
		div3.appendChild(dv);
		c[10]	= c[1]+", "+c[2]+", "+c[3];
		c[10]	= c[10].toLowerCase();
	}
	div2.appendChild(div3);
	d.getElementById("userselectortab_sel_num").innerHTML	= this.selnum;
	this.container.appendChild(div2);
	this.tab(this.seltab);
	this.search("");
	this.onload();
	return true;
};

UserSelector.prototype.tab	= function(tb)
{
	d.getElementById("userselectortab_all").className	= "";
	d.getElementById("userselectortab_sel").className	= "";
	d.getElementById("userselectortab_"+tb).className	= "slctd";
	d.getElementById("userselector_searchinp").value	= this.texts.searchinp;
	this.seltab	= tb;
	this.search("");
};
UserSelector.prototype.search	= function(txt)
{
	txt	= this.trim(txt);
	txt	= txt.toLowerCase();
	if( txt == this.texts.searchinp ) {
		txt	= "";
	}
	if( txt.length < 2 ) {
		txt	= "";
	}
	var i, c, dv, tmp, str, pos;
	for(i=0; i<this.data.length; i++) {
		c	= this.data[i];
		dv	= d.getElementById("uselector_cnt_"+c[0]);
		dv.style.display	= "none";
		if( this.seltab=="sel" && c[5]==0 ) {
			continue;
		}
		if( txt == "" ) {
			tmp	= d.getElementById("uselector_unm_"+c[0]);
			tmp.innerHTML	= tmp.getAttribute("utxt");
			tmp	= d.getElementById("uselector_fnm_"+c[0]);
			tmp.innerHTML	= tmp.getAttribute("utxt");
			dv.style.display	= "block";
			continue;
		}
		if( c[10].indexOf(txt) != -1 ) {
			tmp	= d.getElementById("uselector_unm_"+c[0]);
			str	= tmp.getAttribute("utxt");
			pos	= str.toLowerCase().indexOf(txt);
			if( pos != -1 ) {
				str	= str.substr(0,pos) + "<span>" + str.substr(pos,txt.length) + "</span>" + str.substr(pos+txt.length);
			}
			tmp.innerHTML	= str;
			tmp	= d.getElementById("uselector_fnm_"+c[0]);
			str	= tmp.getAttribute("utxt");
			pos	= str.toLowerCase().indexOf(txt);
			if( pos != -1 ) {
				str	= str.substr(0,pos) + "<span>" + str.substr(pos,txt.length) + "</span>" + str.substr(pos+txt.length);
			}
			tmp.innerHTML	= str;
			dv.style.display	= "block";
		}
	}
};

UserSelector.prototype.trim	= function(txt)
{
	if( typeof(txt) != "string" ) { return txt; }
	txt	= txt.replace(/^\s+/, "");
	txt	= txt.replace(/\s+$/, "");
	return txt;
};