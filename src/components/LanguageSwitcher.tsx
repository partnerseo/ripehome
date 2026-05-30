import { useState, useRef, useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import { useNavigate, useLocation } from 'react-router-dom';
import { Globe } from 'lucide-react';
import { languages, type LanguageCode } from '../i18n';
import { switchLangPath } from '../lib/routes';

const LanguageSwitcher = () => {
  const { i18n } = useTranslation();
  const navigate = useNavigate();
  const location = useLocation();
  const [isOpen, setIsOpen] = useState(false);
  const dropdownRef = useRef<HTMLDivElement>(null);

  const currentLang = languages.find(l => l.code === i18n.language) || languages[0];

  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (dropdownRef.current && !dropdownRef.current.contains(event.target as Node)) {
        setIsOpen(false);
      }
    };
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  const changeLanguage = (code: LanguageCode) => {
    const newPath = switchLangPath(location.pathname, code);
    navigate(newPath + location.search);
    setIsOpen(false);
  };

  return (
    <div className="relative" ref={dropdownRef}>
      <button
        onClick={() => setIsOpen(!isOpen)}
        className="flex items-center gap-2 px-3 py-2 rounded-full border border-neutral-200 bg-white/80 backdrop-blur-sm hover:border-[#8B7355]/40 hover:bg-[#F8F6F3] transition-all duration-300 group shadow-sm hover:shadow-md"
        aria-label="Change language"
      >
        <Globe className="w-4 h-4 text-[#8B7355] group-hover:rotate-12 transition-transform duration-300" />
        <span className="text-sm font-medium text-neutral-700 group-hover:text-neutral-900 tracking-wide uppercase">
          {currentLang.code}
        </span>
        <svg
          className={`w-3 h-3 text-neutral-400 transition-transform duration-300 ${isOpen ? 'rotate-180' : ''}`}
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M19 9l-7 7-7-7" />
        </svg>
      </button>

      {/* Dropdown */}
      <div
        className={`absolute right-0 top-full mt-3 bg-white rounded-2xl shadow-2xl border border-neutral-100 overflow-hidden z-50 min-w-[200px] transition-all duration-300 origin-top-right ${
          isOpen
            ? 'opacity-100 scale-100 translate-y-0 pointer-events-auto'
            : 'opacity-0 scale-95 -translate-y-2 pointer-events-none'
        }`}
      >
        {/* Header */}
        <div className="px-4 py-3 bg-gradient-to-r from-[#F8F6F3] to-[#F0ECE5] border-b border-neutral-100">
          <div className="flex items-center gap-2">
            <Globe className="w-3.5 h-3.5 text-[#8B7355]" />
            <span className="text-xs font-semibold text-[#8B7355] uppercase tracking-widest">Language</span>
          </div>
        </div>

        {/* Options */}
        <div className="py-1.5">
          {languages.map((lang) => {
            const isActive = currentLang.code === lang.code;
            return (
              <button
                key={lang.code}
                onClick={() => changeLanguage(lang.code)}
                className={`w-full text-left px-4 py-3 flex items-center gap-3 transition-all duration-200 ${
                  isActive
                    ? 'bg-[#F8F6F3]'
                    : 'hover:bg-neutral-50'
                }`}
              >
                <span className="text-lg leading-none">{lang.flag}</span>
                <div className="flex-1">
                  <span className={`text-sm ${isActive ? 'text-neutral-900 font-semibold' : 'text-neutral-700 font-medium'}`}>
                    {lang.name}
                  </span>
                  <span className={`text-xs ml-2 uppercase tracking-wider ${isActive ? 'text-[#8B7355]' : 'text-neutral-400'}`}>
                    {lang.code}
                  </span>
                </div>
                {isActive && (
                  <div className="w-5 h-5 rounded-full bg-[#8B7355] flex items-center justify-center flex-shrink-0">
                    <svg className="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M5 13l4 4L19 7" />
                    </svg>
                  </div>
                )}
              </button>
            );
          })}
        </div>
      </div>
    </div>
  );
};

export default LanguageSwitcher;
