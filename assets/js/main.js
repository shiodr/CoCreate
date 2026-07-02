document.addEventListener("DOMContentLoaded", () => {
  setupThemeToggle();
  setupNavigation();
  setupRevealMotion();
  setupConfirmDialogs();
  setupValidation();
  setupImagePreviews();
  setupChoiceFieldsets();
  setupProjectLinkFields();
  setupComboboxes();
  setupLiveProjectFilters();
  animateCounters();
});

function setupThemeToggle() {
  const toggle = document.querySelector("[data-theme-toggle]");
  if (!toggle) return;

  const readSavedTheme = () => {
    try {
      return localStorage.getItem("cocreate-theme");
    } catch (error) {
      return null;
    }
  };

  const saveTheme = (theme) => {
    try {
      localStorage.setItem("cocreate-theme", theme);
    } catch (error) {}
  };

  const getTheme = () =>
    document.documentElement.dataset.theme === "dark" ? "dark" : "light";
  const update = () => {
    const isDark = getTheme() === "dark";
    toggle.setAttribute(
      "aria-label",
      isDark ? "Switch to light mode" : "Switch to dark mode",
    );
    toggle.setAttribute("aria-pressed", String(isDark));
  };

  toggle.addEventListener("click", () => {
    const next = getTheme() === "dark" ? "light" : "dark";
    document.documentElement.dataset.theme = next;
    saveTheme(next);
    update();
  });

  if (!readSavedTheme() && window.matchMedia) {
    window
      .matchMedia("(prefers-color-scheme: dark)")
      .addEventListener("change", (event) => {
        document.documentElement.dataset.theme = event.matches
          ? "dark"
          : "light";
        update();
      });
  }

  update();
}

function setupNavigation() {
  const toggle = document.querySelector("[data-nav-toggle]");
  const links = document.querySelector("[data-nav-links]");

  if (!toggle || !links) return;

  toggle.addEventListener("click", () => {
    const isOpen = links.classList.toggle("open");
    toggle.classList.toggle("open", isOpen);
    toggle.setAttribute("aria-expanded", String(isOpen));
  });

  links.querySelectorAll("a").forEach((link) => {
    link.addEventListener("click", () => {
      links.classList.remove("open");
      toggle.classList.remove("open");
      toggle.setAttribute("aria-expanded", "false");
    });
  });
}

function setupRevealMotion() {
  const targets = document.querySelectorAll(
    ".page-head, .filter-bar, .card, .stat, .table-wrap, .quick-actions, .hero-panel, .hero-metrics > div",
  );
  targets.forEach((target) => {
    if (!target.hasAttribute("data-reveal")) {
      target.setAttribute("data-reveal", "");
    }
  });

  if (!("IntersectionObserver" in window)) {
    targets.forEach((target) => target.classList.add("is-visible"));
    return;
  }

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("is-visible");
          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.12 },
  );

  targets.forEach((target) => observer.observe(target));
}

function setupConfirmDialogs() {
  document.querySelectorAll("form[data-confirm]").forEach((form) => {
    form.addEventListener("submit", async (event) => {
      if (form.dataset.confirmed === "true") return;

      event.preventDefault();
      const message = form.getAttribute("data-confirm") || "Are you sure?";
      const confirmed = await showConfirm(message);

      if (confirmed) {
        form.dataset.confirmed = "true";
        if (event.submitter && typeof form.requestSubmit === "function") {
          form.requestSubmit(event.submitter);
        } else {
          form.submit();
        }
      }
    });
  });
}

function showConfirm(message) {
  return new Promise((resolve) => {
    const backdrop = document.createElement("div");
    backdrop.className = "confirm-backdrop";
    backdrop.innerHTML = `
      <section class="confirm-dialog" role="dialog" aria-modal="true" aria-labelledby="confirm-title">
        <h2 id="confirm-title">Confirm action</h2>
        <p></p>
        <div class="confirm-actions">
          <button class="btn btn-ghost" type="button" data-confirm-cancel>Cancel</button>
          <button class="btn btn-danger" type="button" data-confirm-ok>Confirm</button>
        </div>
      </section>
    `;

    const messageNode = backdrop.querySelector("p");
    const cancel = backdrop.querySelector("[data-confirm-cancel]");
    const ok = backdrop.querySelector("[data-confirm-ok]");
    messageNode.textContent = message;
    document.body.appendChild(backdrop);
    ok.focus();

    const onKeydown = (event) => {
      if (event.key === "Escape") {
        close(false);
      }
    };

    const close = (value) => {
      document.removeEventListener("keydown", onKeydown);
      backdrop.remove();
      resolve(value);
    };

    cancel.addEventListener("click", () => close(false));
    ok.addEventListener("click", () => close(true));
    backdrop.addEventListener("click", (event) => {
      if (event.target === backdrop) close(false);
    });
    document.addEventListener("keydown", onKeydown);
  });
}

function setupValidation() {
  document.querySelectorAll("form[data-validate]").forEach((form) => {
    form.addEventListener("submit", (event) => {
      const invalid = [...form.querySelectorAll("[required]")].find(
        (field) => !String(field.value || "").trim(),
      );
      if (invalid) {
        event.preventDefault();
        invalid.focus();
        invalid.classList.add("field-error");
        return;
      }

      const invalidChoiceGroup = [
        ...form.querySelectorAll('[data-choice-required="true"]'),
      ].find(
        (fieldset) => !fieldset.querySelector('input[type="checkbox"]:checked'),
      );
      if (invalidChoiceGroup) {
        event.preventDefault();
        invalidChoiceGroup.classList.add("field-error");
        invalidChoiceGroup.querySelector("[data-choice-add]")?.focus();
      }
    });
  });

  document.querySelectorAll("input, textarea, select").forEach((field) => {
    field.addEventListener("input", () =>
      field.classList.remove("field-error"),
    );
  });

  document
    .querySelectorAll('input[type="password"][minlength]')
    .forEach((field) => {
      const hint = document.createElement("span");
      hint.className = "field-hint";
      hint.textContent = `Minimum ${field.minLength} characters`;
      field.insertAdjacentElement("afterend", hint);
    });
}

function setupImagePreviews() {
  document
    .querySelectorAll('input[type="file"][accept*="image"]')
    .forEach((input) => {
      input.addEventListener("change", () => {
        const file = input.files && input.files[0];
        let preview = input.parentElement.querySelector(".js-file-preview");

        if (!file) {
          if (preview) preview.remove();
          return;
        }

        if (!preview) {
          preview = document.createElement("img");
          preview.className = "js-file-preview";
          preview.alt = "Selected image preview";
          input.insertAdjacentElement("afterend", preview);
        }

        preview.src = URL.createObjectURL(file);
        preview.onload = () => URL.revokeObjectURL(preview.src);
      });
    });
}

function setupChoiceFieldsets() {
  document.querySelectorAll("[data-choice-fieldset]").forEach((fieldset) => {
    const grid = fieldset.querySelector("[data-choice-grid]");
    const input = fieldset.querySelector("[data-choice-input]");
    const addButton = fieldset.querySelector("[data-choice-add]");
    const addLabel = addButton?.querySelector("[data-choice-add-label]");
    const name = fieldset.dataset.choiceName;

    if (!grid || !input || !addButton || !name) return;

    const collapsedLabel = addLabel?.textContent || "Add";
    const setAdding = (isAdding) => {
      input.hidden = !isAdding;
      addButton.classList.toggle("is-active", isAdding);
      addButton.setAttribute("aria-expanded", String(isAdding));
      if (addLabel) addLabel.textContent = isAdding ? "Add" : collapsedLabel;
      if (isAdding) {
        syncInputWidth();
        input.focus();
      }
    };

    const syncInputWidth = () => {
      const value = input.value.trim();
      const chars = Math.max(14, value.length + 2);
      input.style.width = `${chars}ch`;
    };

    const normalize = (value) => value.trim().toLowerCase();
    const findExisting = (value) =>
      [...grid.querySelectorAll('input[type="checkbox"]')].find(
        (checkbox) => normalize(checkbox.value) === normalize(value),
      );

    const createChoice = (value) => {
      const label = document.createElement("label");
      label.className = "choice-pill";

      const checkbox = document.createElement("input");
      checkbox.type = "checkbox";
      checkbox.name = `${name}[]`;
      checkbox.value = value;
      checkbox.checked = true;

      const text = document.createElement("span");
      text.textContent = value;

      label.append(checkbox, text);
      grid.appendChild(label);
      fieldset.classList.remove("field-error");
    };

    const addChoice = () => {
      const value = input.value.trim();
      if (!value) {
        input.focus();
        return false;
      }

      const existing = findExisting(value);
      if (existing) {
        existing.checked = true;
      } else {
        createChoice(value);
      }

      fieldset.classList.remove("field-error");
      input.value = "";
      syncInputWidth();
      setAdding(false);
      return true;
    };

    syncInputWidth();
    setAdding(false);
    addButton.addEventListener("click", (event) => {
      event.preventDefault();
      if (input.hidden) {
        setAdding(true);
        return;
      }
      addChoice();
    });
    input.addEventListener("input", syncInputWidth);
    input.addEventListener("keydown", (event) => {
      if (event.key === "Enter") {
        event.preventDefault();
        addChoice();
      }
      if (event.key === "Escape") {
        event.preventDefault();
        input.value = "";
        syncInputWidth();
        setAdding(false);
        addButton.focus();
      }
    });

    grid.addEventListener("change", () =>
      fieldset.classList.remove("field-error"),
    );
  });
}

function setupProjectLinkFields() {
  document.querySelectorAll("[data-project-links]").forEach((group) => {
    const list = group.querySelector("[data-project-link-list]");
    const addButton = group.querySelector("[data-project-link-add]");
    const template = group.querySelector("[data-project-link-template]");

    if (!list || !addButton || !template) return;

    const focusFirstInput = (row) => {
      row?.querySelector('input[name="project_links[label][]"]')?.focus();
    };

    addButton.addEventListener("click", () => {
      const fragment = template.content.cloneNode(true);
      list.appendChild(fragment);
      focusFirstInput(list.lastElementChild);
    });

    list.addEventListener("click", (event) => {
      const removeButton = event.target.closest("[data-project-link-remove]");
      if (!removeButton) return;

      const row = removeButton.closest("[data-project-link-row]");
      if (!row) return;

      const rows = list.querySelectorAll("[data-project-link-row]");
      if (rows.length <= 1) {
        row.querySelectorAll("input").forEach((input) => {
          input.value = "";
          input.classList.remove("field-error");
        });
        focusFirstInput(row);
        return;
      }

      const nextRow = row.nextElementSibling || row.previousElementSibling;
      row.remove();
      focusFirstInput(nextRow);
    });
  });
}

function setupComboboxes() {
  document.querySelectorAll("[data-combobox]").forEach((combobox) => {
    const input = combobox.querySelector("[data-combobox-input]");
    const toggle = combobox.querySelector("[data-combobox-toggle]");
    const list = combobox.querySelector("[data-combobox-list]");
    const options = [...combobox.querySelectorAll("[data-combobox-option]")];
    const empty = combobox.querySelector("[data-combobox-empty]");
    let activeIndex = -1;

    if (!input || !toggle || !list || !options.length) return;

    const visibleOptions = () => options.filter((option) => !option.hidden);

    const setActive = (index) => {
      const visible = visibleOptions();
      if (!visible.length || index < 0) {
        activeIndex = -1;
      } else {
        activeIndex = index % visible.length;
      }
      options.forEach((option) => option.classList.remove("is-active"));
      if (activeIndex >= 0) {
        visible[activeIndex].classList.add("is-active");
        visible[activeIndex].scrollIntoView({ block: "nearest" });
      }
    };

    const openList = () => {
      list.hidden = false;
      input.setAttribute("aria-expanded", "true");
      combobox.classList.add("is-open");
    };

    const closeList = () => {
      list.hidden = true;
      input.setAttribute("aria-expanded", "false");
      combobox.classList.remove("is-open");
      setActive(-1);
    };

    const filterOptions = () => {
      const query = input.value.trim().toLowerCase();
      let visibleCount = 0;

      options.forEach((option) => {
        const matches = option.dataset.value.toLowerCase().includes(query);
        option.hidden = !matches;
        if (matches) visibleCount += 1;
      });

      if (empty) empty.hidden = visibleCount > 0;
      setActive(visibleCount ? 0 : -1);
    };

    const selectOption = (option) => {
      input.value = option.dataset.value;
      input.classList.remove("field-error");
      closeList();
      input.focus();
    };

    input.addEventListener("focus", () => {
      filterOptions();
      openList();
    });
    input.addEventListener("input", () => {
      filterOptions();
      openList();
    });
    input.addEventListener("keydown", (event) => {
      const visible = visibleOptions();
      if (event.key === "ArrowDown") {
        event.preventDefault();
        if (list.hidden) openList();
        setActive(activeIndex + 1);
      }
      if (event.key === "ArrowUp") {
        event.preventDefault();
        if (list.hidden) openList();
        setActive(activeIndex - 1);
      }
      if (event.key === "Enter" && !list.hidden && activeIndex >= 0) {
        event.preventDefault();
        selectOption(visible[activeIndex]);
      }
      if (event.key === "Escape") {
        closeList();
      }
    });

    toggle.addEventListener("click", () => {
      if (list.hidden) {
        filterOptions();
        openList();
        input.focus();
      } else {
        closeList();
      }
    });

    options.forEach((option) => {
      option.addEventListener("click", () => selectOption(option));
    });

    document.addEventListener("click", (event) => {
      if (!combobox.contains(event.target)) closeList();
    });
  });
}

function setupLiveProjectFilters() {
  document.querySelectorAll(".filter-bar").forEach((form) => {
    const grid = form.nextElementSibling;
    if (!grid || !grid.classList.contains("cards-grid")) return;

    const cards = [...grid.querySelectorAll(".project-card")];
    if (!cards.length) return;

    const summary = document.createElement("p");
    summary.className = "live-filter-summary";
    form.appendChild(summary);

    const update = () => {
      const query = (form.querySelector('[name="q"]')?.value || "")
        .trim()
        .toLowerCase();
      const skill = (form.querySelector('[name="skill"]')?.value || "")
        .trim()
        .toLowerCase();
      const status = (
        form.querySelector('[name="status"]')?.value || ""
      ).trim();
      let visible = 0;

      cards.forEach((card) => {
        const text = card.textContent.toLowerCase();
        const escapedStatus =
          window.CSS && CSS.escape
            ? CSS.escape(status)
            : status.replace(/[^a-z0-9_-]/gi, "\\$&");
        const statusMatch =
          !status || card.querySelector(`.status-${escapedStatus}`);
        const queryMatch = !query || text.includes(query);
        const skillMatch = !skill || text.includes(skill);
        const show = Boolean(statusMatch && queryMatch && skillMatch);
        card.hidden = !show;
        if (show) visible += 1;
      });

      summary.textContent = `Showing ${visible} of ${cards.length} project${cards.length === 1 ? "" : "s"}`;
    };

    form.querySelectorAll("input, select").forEach((field) => {
      field.addEventListener("input", update);
      field.addEventListener("change", update);
    });

    update();
  });
}

function animateCounters() {
  const counters = document.querySelectorAll(".num");
  counters.forEach((counter) => {
    const value = Number(counter.textContent.trim());
    if (!Number.isFinite(value)) return;

    let frame = 0;
    const frames = 28;
    const tick = () => {
      frame += 1;
      const progress = Math.min(frame / frames, 1);
      counter.textContent = String(Math.round(value * progress));
      if (progress < 1) requestAnimationFrame(tick);
    };
    tick();
  });
}
