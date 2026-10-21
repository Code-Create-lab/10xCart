/* 10xCart landing page interactions (no dependencies) */
// Set up right after the page's first frame is painted, so this script never delays the first paint.
(function (init) {
	requestAnimationFrame(function () {
		setTimeout(init, 0);
	});
})(function () {
	"use strict";

	var $ = function (sel, ctx) {
		return (ctx || document).querySelector(sel);
	};
	var $$ = function (sel, ctx) {
		return Array.prototype.slice.call((ctx || document).querySelectorAll(sel));
	};
	var reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

	/* ---------- Header: solid background after scroll ---------- */
	var header = $("#site-header");
	var ticking = false;
	function onScroll() {
		if (ticking) return;
		ticking = true;
		window.requestAnimationFrame(function () {
			header.classList.toggle("is-scrolled", window.scrollY > 12);
			ticking = false;
		});
	}
	window.addEventListener("scroll", onScroll, { passive: true });
	onScroll();

	/* ---------- Mobile menu ---------- */
	var toggle = $("#menu-toggle");
	var menu = $("#mobile-menu");
	function setMenu(open) {
		toggle.setAttribute("aria-expanded", String(open));
		toggle.setAttribute("aria-label", open ? "Close menu" : "Open menu");
		menu.classList.toggle("is-open", open);
		header.classList.toggle("menu-open", open);
	}
	toggle.addEventListener("click", function () {
		setMenu(toggle.getAttribute("aria-expanded") !== "true");
	});
	$$("a", menu).forEach(function (link) {
		link.addEventListener("click", function () {
			setMenu(false);
		});
	});
	document.addEventListener("keydown", function (e) {
		if (e.key === "Escape" && menu.classList.contains("is-open")) {
			setMenu(false);
			toggle.focus();
		}
	});
	document.addEventListener("click", function (e) {
		if (menu.classList.contains("is-open") && !menu.contains(e.target) && !toggle.contains(e.target)) {
			setMenu(false);
		}
	});
	window.addEventListener("resize", function () {
		if (window.innerWidth >= 1080 && menu.classList.contains("is-open")) setMenu(false);
	});

	/* ---------- Scroll reveal ---------- */
	var revealEls = $$(".reveal");
	if ("IntersectionObserver" in window && !reduceMotion) {
		var revealObserver = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						entry.target.classList.add("is-visible");
						revealObserver.unobserve(entry.target);
					}
				});
			},
			{ rootMargin: "0px 0px -8% 0px", threshold: 0.08 }
		);
		revealEls.forEach(function (el) {
			revealObserver.observe(el);
		});
	} else {
		revealEls.forEach(function (el) {
			el.classList.add("is-visible");
		});
	}

	/* ---------- Active nav link ---------- */
	var navLinks = $$(".nav-links a");
	if ("IntersectionObserver" in window && navLinks.length) {
		var linkFor = {};
		navLinks.forEach(function (a) {
			linkFor[a.getAttribute("href").slice(1)] = a;
		});
		var navObserver = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					var link = linkFor[entry.target.id];
					if (!link || !entry.isIntersecting) return;
					navLinks.forEach(function (a) {
						a.classList.remove("is-active");
						a.removeAttribute("aria-current");
					});
					link.classList.add("is-active");
					link.setAttribute("aria-current", "true");
				});
			},
			{ rootMargin: "-45% 0px -50% 0px" }
		);
		Object.keys(linkFor).forEach(function (id) {
			var section = document.getElementById(id);
			if (section) navObserver.observe(section);
		});
	}

	/* ---------- Features: show all ---------- */
	var featuresToggle = $("#features-toggle");
	var featureGrid = $("#feature-grid");
	if (featuresToggle && featureGrid) {
		featuresToggle.addEventListener("click", function () {
			var expanded = featuresToggle.getAttribute("aria-expanded") === "true";
			featuresToggle.setAttribute("aria-expanded", String(!expanded));
			featureGrid.classList.toggle("is-expanded", !expanded);
			$("span", featuresToggle).textContent = expanded ? "Show all 18 features" : "Show fewer features";
		});
	}

	/* ---------- Admin tabs (WAI-ARIA tabs pattern) ---------- */
	var tabs = $$('#admin [role="tab"]');
	function selectTab(tab, focus) {
		tabs.forEach(function (t) {
			var selected = t === tab;
			t.setAttribute("aria-selected", String(selected));
			t.tabIndex = selected ? 0 : -1;
			document.getElementById(t.getAttribute("aria-controls")).hidden = !selected;
		});
		if (focus) tab.focus();
		tab.scrollIntoView({ block: "nearest", inline: "nearest", behavior: reduceMotion ? "auto" : "smooth" });
	}
	tabs.forEach(function (tab, i) {
		tab.addEventListener("click", function () {
			selectTab(tab, false);
		});
		tab.addEventListener("keydown", function (e) {
			var next = null;
			if (e.key === "ArrowRight") next = tabs[(i + 1) % tabs.length];
			else if (e.key === "ArrowLeft") next = tabs[(i - 1 + tabs.length) % tabs.length];
			else if (e.key === "Home") next = tabs[0];
			else if (e.key === "End") next = tabs[tabs.length - 1];
			if (next) {
				e.preventDefault();
				selectTab(next, true);
			}
		});
	});

	/* ---------- App designs: one iPhone frame, screens slide underneath ---------- */
	var visuals = $("#visuals");
	if (visuals) {
		var vSlides = $$(".v-stage-mobile .v-slide", visuals); // carry caption data
		var vAllSlides = $$(".v-slide", visuals); // mobile + desktop, linked by data-i
		var vStages = $$(".v-stage", visuals);
		var vChips = $$(".v-chip", visuals);
		var vChipBox = $(".v-chips", visuals);
		var vCaption = $("#v-caption");
		var vPlay = $("#v-play");
		var vCount = vSlides.length;
		var vActive = 0;
		var vTimer = null;
		var vPaused = reduceMotion;
		var vHold = false; // pointer over the phone or keyboard focus inside the carousel
		var vInView = false;
		var vSkipClick = false;
		var pad2 = function (n) {
			return (n < 10 ? "0" : "") + n;
		};

		// animate is false on the first render: it must not read layout (offsetWidth, scrollWidth) or start
		// a smooth scroll, because that would delay the page's first paint.
		var vRender = function (animate) {
			vAllSlides.forEach(function (slide) {
				var o = Number(slide.dataset.i) - vActive;
				if (o > vCount / 2) o -= vCount;
				if (o < -vCount / 2) o += vCount;
				slide.style.setProperty("--o", o);
				slide.dataset.pos = Math.abs(o) > 2 ? "far" : String(Math.abs(o));
				slide.setAttribute("aria-hidden", String(o !== 0));
			});
			vChips.forEach(function (chip, i) {
				chip.setAttribute("aria-current", String(i === vActive));
			});

			var slide = vSlides[vActive];
			$(".v-count", vCaption).textContent = pad2(vActive + 1) + " / " + pad2(vCount);
			$("h3", vCaption).textContent = slide.dataset.title;
			$("p", vCaption).textContent = slide.dataset.text;
			if (!animate) return;
			vCaption.classList.remove("is-swap");
			void vCaption.offsetWidth; // restart the caption fade
			vCaption.classList.add("is-swap");

			// Keep the active chip in view on mobile without scrolling the page.
			if (vChipBox.scrollWidth > vChipBox.clientWidth) {
				var chip = vChips[vActive];
				vChipBox.scrollTo({ left: chip.offsetLeft - (vChipBox.clientWidth - chip.offsetWidth) / 2, behavior: reduceMotion ? "auto" : "smooth" });
			}
		};

		var vSchedule = function () {
			clearInterval(vTimer);
			vTimer = null;
			if (!vPaused && !vHold && vInView && !document.hidden) {
				vTimer = setInterval(function () {
					vGo(vActive + 1, false);
				}, 3500);
			}
		};

		var vGo = function (i, fromUser) {
			vActive = (i + vCount) % vCount;
			vRender(true);
			if (fromUser) vSchedule();
		};

		var vSetPaused = function (paused) {
			vPaused = paused;
			vPlay.setAttribute("aria-label", paused ? "Play slideshow" : "Pause slideshow");
			$("use", vPlay).setAttribute("href", paused ? "#i-play" : "#i-pause");
			$("span", vPlay).textContent = paused ? "Play" : "Pause";
			vCaption.setAttribute("aria-live", paused ? "polite" : "off");
			vSchedule();
		};

		vPlay.addEventListener("click", function () {
			vSetPaused(!vPaused);
		});
		$$("[data-v]", visuals).forEach(function (btn) {
			btn.addEventListener("click", function () {
				vGo(vActive + Number(btn.dataset.v), true);
			});
		});
		vChips.forEach(function (chip, i) {
			chip.addEventListener("click", function () {
				vGo(i, true);
			});
		});
		vChipBox.addEventListener("keydown", function (e) {
			var step = e.key === "ArrowRight" ? 1 : e.key === "ArrowLeft" ? -1 : 0;
			if (!step) return;
			e.preventDefault();
			vGo(vActive + step, true);
			vChips[vActive].focus();
		});
		vAllSlides.forEach(function (slide) {
			slide.addEventListener("click", function () {
				var i = Number(slide.dataset.i);
				if (vSkipClick) {
					vSkipClick = false;
					return;
				}
				if (i !== vActive) vGo(i, true);
			});
		});

		// Swipe (horizontal only; vertical page scroll stays native via touch-action: pan-y)
		var startX = null;
		var startY = 0;
		vStages.forEach(function (stage) {
			stage.addEventListener("pointerdown", function (e) {
				startX = e.clientX;
				startY = e.clientY;
				vSkipClick = false;
			});
			stage.addEventListener("pointerup", function (e) {
				if (startX === null) return;
				var dx = e.clientX - startX;
				var dy = e.clientY - startY;
				startX = null;
				if (Math.abs(dx) > 40 && Math.abs(dx) > Math.abs(dy)) {
					vSkipClick = true;
					vGo(vActive + (dx < 0 ? 1 : -1), true);
				}
			});
			stage.addEventListener("pointercancel", function () {
				startX = null;
			});

			// Pause while the visitor is looking at the device
			stage.addEventListener("mouseenter", function () {
				vHold = true;
				vSchedule();
			});
			stage.addEventListener("mouseleave", function () {
				vHold = false;
				vSchedule();
			});
		});

		// Device switch: Mobile (iPhone) / Desktop (monitor), same design index in both
		var vTabs = $$('.v-views [role="tab"]', visuals);
		var vWarmDesktop = function () {
			$$("#v-stage-desktop img").forEach(function (img) {
				img.loading = "eager";
			});
		};
		var vSelectView = function (tab, focus) {
			vTabs.forEach(function (t) {
				var on = t === tab;
				t.setAttribute("aria-selected", String(on));
				t.tabIndex = on ? 0 : -1;
				document.getElementById(t.getAttribute("aria-controls")).hidden = !on;
			});
			if (tab.id === "v-tab-desktop") vWarmDesktop();
			if (focus) tab.focus();
			vHold = false; // the hidden stage never fires mouseleave
			vSchedule();
		};
		vTabs.forEach(function (tab, i) {
			tab.addEventListener("click", function () {
				vSelectView(tab, false);
			});
			tab.addEventListener("keydown", function (e) {
				var next = null;
				if (e.key === "ArrowRight") next = vTabs[(i + 1) % vTabs.length];
				else if (e.key === "ArrowLeft") next = vTabs[(i - 1 + vTabs.length) % vTabs.length];
				else if (e.key === "Home") next = vTabs[0];
				else if (e.key === "End") next = vTabs[vTabs.length - 1];
				if (next) {
					e.preventDefault();
					vSelectView(next, true);
				}
			});
		});
		// Start fetching desktop designs as soon as the visitor shows interest in that tab
		$("#v-tab-desktop").addEventListener("pointerenter", vWarmDesktop, { once: true });
		$("#v-tab-desktop").addEventListener("focus", vWarmDesktop, { once: true });

		// Pause while navigating the carousel with the keyboard
		visuals.addEventListener("focusin", function (e) {
			var keyboard = true;
			try {
				keyboard = e.target.matches(":focus-visible");
			} catch (err) {}
			if (e.target !== vPlay && keyboard) {
				vHold = true;
				vSchedule();
			}
		});
		visuals.addEventListener("focusout", function (e) {
			if (!visuals.contains(e.relatedTarget)) {
				vHold = false;
				vSchedule();
			}
		});
		document.addEventListener("visibilitychange", vSchedule);
		if ("IntersectionObserver" in window) {
			new IntersectionObserver(
				function (entries) {
					vInView = entries[0].isIntersecting;
					vSchedule();
				},
				{ threshold: 0.35 }
			).observe(visuals);
		}

		vRender(false);
		vSetPaused(vPaused);
	}

	/* ---------- Pricing: single / multi vendor ---------- */
	var pricing = $("#pricing");
	var modeBtns = $$(".segmented button");
	modeBtns.forEach(function (btn) {
		btn.addEventListener("click", function () {
			pricing.dataset.mode = btn.dataset.mode;
			modeBtns.forEach(function (b) {
				b.setAttribute("aria-pressed", String(b === btn));
			});
		});
	});

	/* Plan buttons pre-fill the contact form */
	var interestSelect = $("#f-interest");
	var planSelect = $("#f-plan");
	var quotePlan = ""; // last "Get a quote" plan clicked, sent with the lead
	$$("[data-plan]").forEach(function (btn) {
		btn.addEventListener("click", function () {
			planSelect.value = quotePlan = btn.dataset.plan;
			interestSelect.value = pricing.dataset.mode === "multi" ? "Multi-vendor marketplace" : "Single vendor store";
			clearError(interestSelect);
		});
	});

	/* ---------- Visitor context (sent with the lead form) ---------- */
	// Tells the sales team how the visitor found the site and what they looked at.
	// Stored only in this browser (localStorage) and sent only when the form is submitted.
	// The server keeps just the fields it knows: app/Support/Leads/VisitorContext.php
	var visitor = (function () {
		var KEY = "10xcart.visitor";
		var NEW_VISIT_AFTER = 30 * 60 * 1000; // 30 min without activity starts a new visit (same rule as Google Analytics)
		var now = Date.now();
		var landing = location.pathname + location.search;
		var saved = {};
		try {
			saved = JSON.parse(localStorage.getItem(KEY)) || {};
		} catch (e) {}

		// Where this visit came from: another website, UTM tags, an ad click
		function currentTouch() {
			var touch = { at: new Date(now).toISOString(), landing: landing };
			try {
				var params = new URLSearchParams(location.search);
				if (document.referrer && new URL(document.referrer).host !== location.host) touch.referrer = document.referrer;
				["utm_source", "utm_medium", "utm_campaign", "utm_term", "utm_content"].forEach(function (key) {
					if (params.get(key)) touch[key] = params.get(key);
				});
				["gclid", "gbraid", "wbraid", "fbclid", "msclkid"].some(function (key) {
					return params.has(key) && (touch.click = key);
				});
			} catch (e) {}
			return touch;
		}

		var newCampaign = /[?&](utm_|gclid|gbraid|wbraid|fbclid|msclkid)/.test(location.search) && (!saved.last_touch || saved.last_touch.landing !== landing);
		if (!saved.last_seen || now - saved.last_seen > NEW_VISIT_AFTER || newCampaign) {
			saved.visits = (saved.visits || 0) + 1;
			saved.last_touch = currentTouch();
			saved.first_touch = saved.first_touch || saved.last_touch;
			saved.first_seen = saved.first_seen || saved.last_touch.at;
		}
		saved.last_seen = now;
		try {
			localStorage.setItem(KEY, JSON.stringify(saved));
		} catch (e) {}

		// Sections the visitor scrolled to, in order (counted when they cross the middle of the screen)
		var viewed = [];
		if ("IntersectionObserver" in window) {
			var sectionObserver = new IntersectionObserver(
				function (entries) {
					entries.forEach(function (entry) {
						if (entry.isIntersecting && viewed.indexOf(entry.target.id) === -1) viewed.push(entry.target.id);
					});
				},
				{ rootMargin: "-50% 0px -50% 0px" }
			);
			$$("main > section[id]").forEach(function (section) {
				sectionObserver.observe(section);
			});
		}

		// Chromium browsers can tell the real OS version and phone model (the User-Agent string hides them)
		var device = { touch: navigator.maxTouchPoints > 0 };
		if (navigator.userAgentData && navigator.userAgentData.getHighEntropyValues) {
			navigator.userAgentData
				.getHighEntropyValues(["platformVersion", "model"])
				.then(function (ua) {
					device.platform = ua.platform;
					device.platform_version = ua.platformVersion;
					device.model = ua.model;
				})
				.catch(function () {});
		}

		return {
			snapshot: function () {
				var timezone = "";
				try {
					timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
				} catch (e) {}
				return {
					page: location.href,
					time_on_page: Math.round(performance.now() / 1000),
					sections: viewed,
					pricing_mode: pricing ? pricing.dataset.mode : "",
					quote_plan: quotePlan,
					timezone: timezone,
					language: navigator.language,
					screen: screen.width + "x" + screen.height,
					device: device,
					visits: saved.visits,
					first_seen: saved.first_seen,
					first_touch: saved.first_touch,
					last_touch: saved.last_touch
				};
			}
		};
	})();

	/* ---------- Lead form ---------- */
	var form = $("#lead-form");
	var statusEl = $("#form-status");

	function fieldWrap(input) {
		return input.closest(".field");
	}
	function setError(input, msg) {
		fieldWrap(input).classList.add("has-error");
		input.setAttribute("aria-invalid", "true");
		input.setAttribute("aria-describedby", input.id + "-error");
		document.getElementById(input.id + "-error").textContent = msg;
	}
	function clearError(input) {
		var err = document.getElementById(input.id + "-error");
		fieldWrap(input).classList.remove("has-error");
		input.removeAttribute("aria-invalid");
		if (err) err.textContent = "";
	}
	function validate(input) {
		var v = input.value.trim();
		if (input.id === "f-name" && v.length < 2) return "Please enter your name.";
		if (input.id === "f-phone" && v.replace(/\D/g, "").length < 10) return "Please enter a valid phone number (at least 10 digits).";
		if (input.id === "f-email" && v && !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v)) return "Please enter a valid email, e.g. name@business.com.";
		if (input.id === "f-interest" && !v) return "Please choose what you're interested in.";
		return "";
	}
	var validated = ["f-name", "f-phone", "f-email", "f-interest"].map(function (id) {
		return document.getElementById(id);
	});
	validated.forEach(function (input) {
		input.addEventListener("blur", function () {
			var msg = validate(input);
			if (msg) setError(input, msg);
			else clearError(input);
		});
		input.addEventListener("input", function () {
			if (fieldWrap(input).classList.contains("has-error") && !validate(input)) clearError(input);
		});
	});

	function showStatus(type, msg) {
		statusEl.className = "form-status is-" + type;
		statusEl.textContent = msg;
	}

	form.addEventListener("submit", function (e) {
		e.preventDefault();
		var firstInvalid = null;
		validated.forEach(function (input) {
			var msg = validate(input);
			if (msg) {
				setError(input, msg);
				if (!firstInvalid) firstInvalid = input;
			} else {
				clearError(input);
			}
		});
		if (firstInvalid) {
			firstInvalid.focus();
			return;
		}

		var data = new FormData(form);
		var submitBtn = $('button[type="submit"]', form);
		var endpoint = form.dataset.endpoint;

		if (!endpoint) {
			// No backend configured yet: hand the enquiry to the visitor's email app.
			var lines = ["Name: " + data.get("name"), "Phone: " + data.get("phone"), "Email: " + (data.get("email") || "-"), "Interested in: " + data.get("interest"), "Plan: " + (data.get("plan") || "Help me choose"), "", data.get("message") || ""];
			window.location.href = "mailto:" + form.dataset.mailto + "?subject=" + encodeURIComponent("Demo request: " + data.get("interest")) + "&body=" + encodeURIComponent(lines.join("\n"));
			showStatus("success", "Opening your email app to send your request. If nothing happens, call us on " + form.dataset.phone + ".");
			return;
		}

		data.append("visitor", JSON.stringify(visitor.snapshot()));
		submitBtn.setAttribute("aria-busy", "true");
		submitBtn.disabled = true;
		fetch(endpoint, { method: "POST", body: data, headers: { Accept: "application/json" } })
			.then(function (res) {
				if (!res.ok) throw new Error(res.status);
				form.reset();
				showStatus("success", "Thank you! Your request has been sent — our team will contact you shortly.");
			})
			.catch(function () {
				showStatus("error", "Sorry, something went wrong. Please try again or call us on " + form.dataset.phone + ".");
			})
			.then(function () {
				submitBtn.removeAttribute("aria-busy");
				submitBtn.disabled = false;
			});
	});

	/* ---------- Mobile sticky CTA ---------- */
	var mobileCta = $("#mobile-cta");
	var hero = $(".hero");
	var contact = $("#contact");
	if (mobileCta && "IntersectionObserver" in window) {
		var heroVisible = true;
		var contactVisible = false;
		var sync = function () {
			mobileCta.classList.toggle("is-visible", !heroVisible && !contactVisible);
		};
		new IntersectionObserver(function (entries) {
			heroVisible = entries[0].isIntersecting;
			sync();
		}).observe(hero);
		new IntersectionObserver(function (entries) {
			contactVisible = entries[0].isIntersecting;
			sync();
		}).observe(contact);
	}

	/* ---------- Sections below the first screen ---------- */
	// CSS skips their layout until they come near the viewport (content-visibility: auto). After the visitor's
	// first interaction they are laid out one per idle moment, so the scrollbar and in-page links become exact.
	// A click on an in-page link lays out the rest at once, so the smooth scroll lands on its target.
	var pendingSections = $$("main > section:not(.hero):not(.section-dark)");
	var idle = window.requestIdleCallback || function (fn) {
		return setTimeout(fn, 50);
	};
	var renderNextSection = function () {
		var section = pendingSections.shift();
		if (!section) return;
		section.classList.add("is-rendered");
		idle(renderNextSection);
	};
	var firstInteraction = ["scroll", "pointerdown", "keydown", "touchstart"];
	var startRendering = function () {
		firstInteraction.forEach(function (e) {
			window.removeEventListener(e, startRendering);
		});
		idle(renderNextSection);
	};
	firstInteraction.forEach(function (e) {
		window.addEventListener(e, startRendering, { passive: true });
	});
	document.addEventListener(
		"click",
		function (e) {
			if (!e.target.closest || !e.target.closest('a[href^="#"]')) return;
			pendingSections.forEach(function (section) {
				section.classList.add("is-rendered");
			});
			pendingSections = [];
		},
		true
	);

	/* ---------- Footer year ---------- */
	var year = $("#year");
	if (year) year.textContent = new Date().getFullYear();
});
